<?php

namespace App\Livewire\Utils;

use App\Models\Member;
use League\Csv\Reader;
use Livewire\Component;
use App\Models\ActiveLoans;
use App\Models\LoanCapture;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportLoansCsv extends Component
{
    use WithFileUploads;

    public $uploadedChunks = 0;
    public $totalChunks = 0;
    public $isProcessing = false;
    public $errorMessage = '';
    public $isCompleted = false;
    public $progress = 0;

    protected $listeners = [
        'process-chunk' => 'handleChunkUploaded',
        'import-complete' => 'handleImportComplete',
        // 'processing-status-changed' => 'handleProcessingStatus',
    ];

    public function handleChunkUploaded($chunkNumber, $totalChunks, $path)
    {
        $this->uploadedChunks = $chunkNumber;
        $this->totalChunks = $totalChunks;

        // Log the chunk upload

        // If all chunks are uploaded, start processing
        $this->processChunk($chunkNumber, $path);
        
    }

    public function handleImportComplete($totalChunks, $totalRows)
    {
        $this->isProcessing = false;
        $this->isCompleted = true;
        $this->errorMessage = '';

        // Log the completion message
        Log::info("Loan Records CSV import completed: {$totalRows} records processed.");

        // Dispatch completion event
        $this->dispatch('show-notification', [
            'type' => 'success',
            'message' => "CSV import completed! Processed {$totalRows} records.",
        ]);

        session()->flash('success', "Loan Records Import completed successfully! Processed {$totalRows} records.");
        
        // Reset the component state
        $this->reset(['isProcessing', 'uploadedChunks', 'totalChunks', 'progress']);
    }

    public function processChunk($chunkNumber, $path)
    {
        DB::beginTransaction();

        try {
            // Process the chunk file
            $this->processChunkFile($path);

            // Commit the transaction
            DB::commit();

            // Log the success message
            Log::info('Successfully processed chunk '. $chunkNumber);

            $this->dispatch('chunk-processed', [
                'chunkNumber' => $chunkNumber,
                'success' => true,
            ]);

            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = 'Processing failed: ' . $e->getMessage();
            Log::error('Error processing chunk: ' . $this->errorMessage);

            $this->dispatch('chunk-processed', [
                'chunkNumber' => $chunkNumber,
                'success' => false,
                'message' => $e->getMessage(),
            ]);

        }
    }

    protected function processChunkFile($chunkPath)
    {
        $fullPath = storage_path('app/' . $chunkPath);

        // use league csv for efficient processing
        $reader = Reader::createFromPath($fullPath, 'r');
        $reader->setDelimiter(','); // Set the delimiter to comma
        $reader->setHeaderOffset(null); // Set the header offset to 0

        $batchSize = 500; // Number of records to process at once
        $batch = [];
        $activeLoanBatch = [];

        // Read the CSV file and process each record
        foreach ($reader->getRecords() as $record) {
            // validate and transform the record
            if(empty($record[0]) || ($record[0] == '-') || stripos($record[0], 'COOP') !== false || stripos($record[0], 'object') !== false) {
                Log::warning("Skipping record due to missing required fields: ", $record);
                continue; // Skip if required fields are missing
            }
            $validatedData = $this->validateRecord($record);

            if ($validatedData) {

                $batch[] = $validatedData;

                // Process active loans
                if($validatedData['status'] == 1) {
                    $activeLoanBatch[] = $this->extractActiveLoan($validatedData);
                }
            }

            // If batch size is reached, insert into the database
            if (count($batch) >= $batchSize) {
                LoanCapture::insert($batch);
                // Insert active loans if any
                if(!empty($activeLoanBatch)) {
                    ActiveLoans::insert($activeLoanBatch);
                }

                // Reset batch and active loan batch
                $batch = []; 
                $activeLoanBatch = [];
            }
        }

        // Insert any remaining records in the batch
        if (!empty($batch)) {
            LoanCapture::insert($batch);
        }
        // Insert any remaining active loans
        if (!empty($activeLoanBatch)) {
            ActiveLoans::insert($activeLoanBatch);
        }

        // clean up the chunk file
        unlink($fullPath);
    }

    protected function validateRecord(array $record): ?array
    {
        // Perform validation and transformation
        $uniqueId = ltrim($record[0], '0'); // first column should be coop Id
        // Check if loan record already exists
        $existingRecord = LoanCapture::where('coopId', $uniqueId)->first();
        if ($existingRecord) {
            // update the existing record
            $existingRecord->update([
                'loanAmount' => $record[1],
                'loanDate' => date('Y-m-d', strtotime($record[2])),
                'guarantor1' => ($record[3] == '-') ? $uniqueId : ($this->checkIfMemberExists(ltrim($record[3], '0')) ? ltrim($record[3], '0') : $uniqueId),
                'guarantor2' => $this->otherGuarantorCheckNullorFilter($record[4]),
                'guarantor3' => $this->otherGuarantorCheckNullorFilter($record[5]),
                'guarantor4' => $this->otherGuarantorCheckNullorFilter($record[6]),
                'status' => $record[7],
                'repaymentDate' => date('Y-m-d', strtotime($record[2]. ' + 540 days')),
                'userId' => auth('admin')->user()->id, // Retrieve admin id
            ]);

            // search in ActiveLoan and update
            $act = ActiveLoans::where('coopId', $uniqueId)->first();
            if($act){
              $act->update([
                'loanAmount' => $record[1],
                'loanBalance' => $record[1],
                'loanDate' => date('Y-m-d', strtotime($record[2])),
                'repaymentDate' => date('Y-m-d', strtotime($record[2]. ' + 540 days')),
                'lastPaymentDate' => date('Y-m-d', strtotime($record[2])),
              ]);
            } else {
              if($existingRecord->status == 1)
                $existingRecord->scopeAddToActiveLoanWithDate();
            }
            
            return null; // Skip this record if it already exists
        }

        // check if member with coopId exists
        if(!$this->checkIfMemberExists($uniqueId)) {
            return null; // Skip if not a member or coopId is not valid
        }

        // Set initial guarantor 1
        // check if guarantor 1 exists or set to null
        $guarantor1 = null;

        if($record[3] == '-' || empty($record[3])) {
            // if guarantor 1 is empty or null(not valid), set to same as uniqueId(current member)
            $guarantor1 = $uniqueId;
        } else {
            // if guarantor 1 is not empty, check if it exists
            // remove leading zeros from the guarantor ID
            $tempGuarantorId = ltrim($record[3], '0');
            // check if the member exists (guarantor 1)
            // if it exists, set to the tempGuarantorId
            // else set to the uniqueId(current member)
            $guarantor1 = $this->checkIfMemberExists($tempGuarantorId) ? $tempGuarantorId : $uniqueId;
        }

        return [
            'coopId' => $uniqueId,
            'loanAmount' => $record[1],
            'loanDate' => date('Y-m-d', strtotime($record[2])),
            'guarantor1' => $guarantor1,
            'guarantor2' => $this->otherGuarantorCheckNullorFilter($record[4]),
            'guarantor3' => $this->otherGuarantorCheckNullorFilter($record[5]),
            'guarantor4' => $this->otherGuarantorCheckNullorFilter($record[6]),
            'status' => $record[7],
            'repaymentDate' => date('Y-m-d', strtotime($record[2]. ' + 540 days')),
            'userId' => auth('admin')->user()->id, // Retrieve admin id
        ];

    }

    /**
     * Extract active loan data from the validated record
     *
     * @param array $validatedRecord
     * @return array|null
     */
    protected function extractActiveLoan(array $validatedRecord): ?array
    {
        return [
            'coopId' => $validatedRecord['coopId'],
            'loanAmount' => $validatedRecord['loanAmount'],
            'loanPaid' => 0,
            'loanBalance' => $validatedRecord['loanAmount'],
            'loanDate' => $validatedRecord['loanDate'],
            'repaymentDate' => $validatedRecord['repaymentDate'],
            'lastPaymentDate' => $validatedRecord['loanDate'],
            'userId' => auth('admin')->user()->id, // Retrieve admin id
        ];
    }

    /**
     * Set the value to null if it is empty or not set
     *
     * @param mixed $value
     * @return mixed
     */
    protected function setNullIfEmpty($value)
    {
        return (empty($value) || !isset($value)) ? null : $value;
    }

    /**
     * Check if the member exists
     *
     * @param mixed $coopId
     * @return bool
     */
    public function checkIfMemberExists($coopId)
    {
      $member = Member::where('coopId', $coopId)->first();
      if(!$member)
        return false;
      return true;
    }

    /**
     * Check if the guarantor is null or filter it
     *
     * @param mixed $guarantor
     * @return mixed
     */
    public function otherGuarantorCheckNullorFilter($data)
    {
        // Set initial guarantor 1
        // check if guarantor 1 exists or set to null
        $guarantor = null;

        if($data != '-' && !empty($data)) {
            // if guarantor 1 is not empty, check if it exists
            // remove leading zeros from the guarantor ID
            $tempGuarantorId = ltrim($data, '0');
            // check if the member exists (guarantor 1)
            // if it exists, set to the tempGuarantorId
            // else set to the null
            $guarantor = $this->checkIfMemberExists($tempGuarantorId) ? $tempGuarantorId : null;
        }

        return $guarantor;
    }

    /**
     * Render the component
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.utils.import-loans-csv');
    }
}
