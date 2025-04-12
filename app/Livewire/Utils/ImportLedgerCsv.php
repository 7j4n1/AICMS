<?php

namespace App\Livewire\Utils;

use App\Models\Member;
use League\Csv\Reader;
use Livewire\Component;
use App\Models\ActiveLoans;
use App\Models\LoanCapture;
use Livewire\WithFileUploads;
use App\Models\PaymentCapture;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SpecialSaveDeduction;

class ImportLedgerCsv extends Component
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
    ];

    /**
     * Handle the chunk upload
     *
     * @param int $chunkNumber
     * @param int $totalChunks
     * @param string $path
     * @return void
     */
    public function handleChunkUploaded($chunkNumber, $totalChunks, $path)
    {
        $this->uploadedChunks = $chunkNumber;
        $this->totalChunks = $totalChunks;

        // Log the chunk upload

        // If all chunks are uploaded, start processing
        $this->processChunk($chunkNumber, $path);
        
    }

    /**
     * Handle the import completion
     *
     * @param int $totalChunks
     * @param int $totalRows
     * @return void
     */
    public function handleImportComplete($totalChunks, $totalRows)
    {
        $this->isProcessing = false;
        $this->isCompleted = true;
        $this->errorMessage = '';

        // Log the completion message
        Log::info("CSV import completed: {$totalRows} records processed.");

        // Dispatch completion event
        $this->dispatch('show-notification', [
            'type' => 'success',
            'message' => "Ledger CSV import completed! Processed {$totalRows} records.",
        ]);

        session()->flash('success', "Ledger Import completed successfully! Processed {$totalRows} records.");
        
        // Reset the component state
        $this->reset(['isProcessing', 'uploadedChunks', 'totalChunks', 'progress']);
    }

    /**
     * Process the chunk file
     *
     * @param int $chunkNumber
     * @param string $path
     * @return void
     */
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

    /**
     * Process the chunk file
     *
     * @param string $chunkPath
     * @return void
     */
    protected function processChunkFile($chunkPath)
    {
        $fullPath = storage_path('app/' . $chunkPath);

        // use league csv for efficient processing
        $reader = Reader::createFromPath($fullPath, 'r');
        $reader->setDelimiter(','); // Set the delimiter to comma
        $reader->setHeaderOffset(null); // Set the header offset to 0

        $batchSize = 500; // Number of records to process at once
        $batch = [];
        $loanUpdatesBatch = [];
        $specialSaveBatch = [];

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
                
                // Track if the record is a loan update
                if($validatedData['loanAmount'] > 0) {
                    $loanUpdatesBatch[] = [
                        'coopId' => $validatedData['coopId'],
                        'loanAmount' => $validatedData['loanAmount'],
                        'paymentDate' => $validatedData['paymentDate'],
                    ];
                }

                if($validatedData['others'] > 0) {
                    $specialSaveBatch[] = [
                        'coopId' => $validatedData['coopId'],
                        'paymentDate' => $validatedData['paymentDate'],
                        'debit' => 0,
                        'type' => 'special',
                        'credit' => $validatedData['others'],
                    ];
                }
            }

            // If batch size is reached, insert into the database
            if (count($batch) >= $batchSize) {
                PaymentCapture::insert($batch);
                SpecialSaveDeduction::insert($specialSaveBatch);
                $this->processLoanUpdatesInBatch($loanUpdatesBatch);
                
                // Reset the batches
                $batch = [];
                $loanUpdatesBatch = []; 
                $specialSaveBatch = [];
            }
        }

        // Insert any remaining records in the batch
        if (!empty($batch)) {
            PaymentCapture::insert($batch);
            SpecialSaveDeduction::insert($specialSaveBatch);
            $this->processLoanUpdatesInBatch($loanUpdatesBatch);
        }

        // clean up the chunk file
        unlink($fullPath);
    }

    /**
     * Validate and transform the record
     *
     * @param array $record
     * @return array|null
     */
    protected function validateRecord(array $record): ?array
    {
        // Perform validation and transformation
        // trim leading zeros from coopId
        $uniqueId = ltrim($record[0], '0');

        // Check if record already exists
        $existingRecord = Member::where('coopId', $uniqueId)->first();

        if(!$existingRecord) {
            return null; // Skip this record if it doesn't exist
        }


        // Check if coopId is empty or invalid
        $loan = $this->setZeroIfEmptyOrTrim($record[5]);
        $share = $this->setZeroIfEmptyOrTrim($record[3]);
        $saving = $this->setZeroIfEmptyOrTrim($record[4]);
        $others = $this->setZeroIfEmptyOrTrim($record[6]);
        // calculate total
        $totalSum = $this->sumFloatArray([$loan, $share, $saving, $others]);
        $paymentDate = date('Y-m-d', strtotime($record[7]));


        return [
            'coopId' => $uniqueId,
            'shareAmount' => $share,
            'savingAmount' => $saving,
            'loanAmount' => $loan,
            'others' => $others,
            'userId' => auth('admin')->user()->id, // Retrieve admin id
            'adminCharge' => 0,
            'totalAmount' => $totalSum,
            'paymentDate' => $paymentDate,
        ];

    }

    protected function processLoanUpdatesInBatch(array $loanUpdate)
    {
        if(empty($loanUpdate)) {
            return;
        }

        // Process the loan updates in batch
        //group by coopId to avoid duplicate updates
        $groupedUpdates = [];
        foreach ($loanUpdate as $update) {
            $coopId = $update['coopId'];

            if (!isset($groupedUpdates[$coopId])) {
                $groupedUpdates[$coopId] = [
                    'totalPayment' => 0,
                    'latestDate' => null
                ];
            }

            $groupedUpdates[$coopId]['totalPayment'] += $update['loanAmount'];

            // Track the latest payment date
            $currentDate = strtotime($update['paymentDate']);
            if (!$groupedUpdates[$coopId]['latestDate'] || 
                $currentDate > strtotime($groupedUpdates[$coopId]['latestDate'] ?? '')) {
                $groupedUpdates[$coopId]['latestDate'] = $update['paymentDate'];
            }
        }

        // fetch all active loans for these coopIds
        $coopIds = array_keys($groupedUpdates);
        $activeLoans = ActiveLoans::whereIn('coopId', $coopIds)->get()->keyBy('coopId');

        $bulkFullyPaid = [];
        $bulkPartiallyPaid = [];

        foreach ($groupedUpdates as $coopId => $update) {
            if(!isset($activeLoans[$coopId])) {
                continue; // Skip if no active loan found
            }

            $activeLoan = $activeLoans[$coopId];
            $totalPayment = $update['totalPayment'];
            $latestDate = $update['latestDate'];

            if($totalPayment >= $activeLoan->loanBalance) {
                // loan is fully paid
                $bulkFullyPaid[] = $activeLoan->coopId;
            } else {
                // loan is partially paid, need to update the balance individually
                $bulkPartiallyPaid[] = [
                    'loan' => $activeLoan,
                    'payment' => $totalPayment,
                    'date' => $latestDate,
                ];
            }
        }

        // update the active loans in bulk
        if(!empty($bulkFullyPaid)) {

            // Move to completed loans table and
            // active loans will be deleted automatically
            LoanCapture::whereIn('coopId', $bulkFullyPaid)
                ->get()
                ->each(function ($loan) {
                    $loan->completedLoan();
                });
        
        }

        // update the active loans individually for partially paid
        foreach ($bulkPartiallyPaid as $update) {
            $payment = $update['payment'];
            $date = $update['date'];

            // update the loan balance
            $update['loan']->setPayment($payment, $date);
        }
    }

    /**
     * Sum an array of float values
     *
     * @param array $values
     * @return float
     */
    protected function sumFloatArray(array $values): float
    {
        return array_reduce($values, function ($prev, $item) {
            return $prev + (float)$item;
        }, 0); 
    }

    /**
     * Set value to Zero(0) if empty
     * or trim leading zeros
     * 
     * @param mixed $value
     * @return mixed
     */
    protected function setZeroIfEmptyOrTrim($value)
    {
        return (empty($value) || ($value == '-')) ? 0 : (float)ltrim($value, '0');
    }
    
    /**
     * Render the component
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.utils.import-ledger-csv');
    }
}
