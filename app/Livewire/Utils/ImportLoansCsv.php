<?php

namespace App\Livewire\Utils;

use App\Models\Member;
use Livewire\Component;
use Illuminate\Bus\Batch;
use App\Models\ActiveLoans;
use App\Models\LoanCapture;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use App\Jobs\RunCsvImportJob;
use Livewire\WithFileUploads;
use App\Traits\LivewireCsvImporter;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ImportLoansCsv extends Component
{
    use WithFileUploads, LivewireCsvImporter;

    public $csvFile;
    public $reportPath;
    public $batchId = null;
    public $progress = 0;
    public $status = 'idle'; // idle, uploading, processing, completed, failed
    public $statusMessage = '';
    public $processedRows = 0;
    public $totalRows = 0;
    public $validRows = 0;
    public $invalidRows = 0;
    public $totalJobs = 0;
    public $completedJobs = 0;
    public $failedJobs = 0;
    public $processingErrors = [];
    public $downloadLinks = [];
    public $progressKey;

    protected $rules = [
        'csvFile' => 'required|file|mimes:csv,txt|max:10048'
    ];

    protected $messages = [
        'csvFile.required' => 'Please select a CSV file.',
        'csvFile.file' => 'The selected file is not valid.',
        'csvFile.mimes' => 'Please select a CSV or TXT file only.',
        'csvFile.max' => 'The file size must not exceed 10MB.',
    ];

    public function updatedCsvFile()
    {
        // Reset any previous validation errors
        $this->resetErrorBag();
        
        // Validate the uploaded file
        $this->validateOnly('csvFile', [
            'csvFile' => $this->rules['csvFile']
        ], $this->messages);
    }

    public function startImport()
    {
        // Validate before processing
        $this->validate();

        $this->status = 'uploading';
        $this->statusMessage = 'Reading and chunking CSV file...';

        // Reset any previous errors
        $this->resetErrorBag();

        try {
            // Store the CSV file temporarily
            $filename = Str::uuid() . '.' . $this->csvFile->getClientOriginalExtension();
            $path = $this->csvFile->storeAs('csv_imports', $filename);

            $fileContent = Storage::get($path);
            // Convert the file data into an array of lines
            $rows = explode(PHP_EOL, $fileContent);

            $this->totalRows = count($rows);

            if ($this->totalRows === 0) {
                throw new \Exception('CSV file is empty or contains no data rows.');
            }

            // chunk the file data into manageable pieces
            $chunks = array_chunk($rows, 100);
            $this->totalJobs = count($chunks);

            $this->status = 'processing';
            $this->statusMessage = 'Creating batch jobs for processing...';

            $batchKey = 'csv-import-' . Str::uuid();

            // Initialize cache counters to 0
            $cachePrefix = "batch_results_{$batchKey}";
            Cache::put("{$cachePrefix}_valid", 0, now()->addHours(12));
            Cache::put("{$cachePrefix}_invalid", 0, now()->addHours(12));
            Cache::put("{$cachePrefix}_errors", 0, now()->addHours(12));
            

            $jobs = [];

            

            foreach ($chunks as $chunkIndex => $chunk) {

                $jobs[]  = new RunCsvImportJob(
                    $chunk,
                    $batchKey,
                    static::class,
                    auth('admin')->user()->id
                );

            }

            // Store the file path in cache for cleanup later
            Cache::put("temp_file_{$batchKey}", $path, now()->addHours(12));


            $batch = Bus::batch($jobs)
                ->name("Import Loan CSV processing - {$batchKey}")
                ->allowFailures()
                ->finally(function (Batch $batch) use ($batchKey) {
                    $tempFilePath = Cache::get("temp_file_{$batchKey}");
                    if ($tempFilePath && Storage::exists($tempFilePath)) {
                        Storage::delete($tempFilePath);
                        Cache::forget("temp_file_{$batchKey}");
                    }
                })
                ->dispatch();

            $this->batchId = $batch->id;

            // Store the batch key in cache for later retrieval
            Cache::put("batch_key_{$this->batchId}", $batchKey, now()->addHours(12));

            
            // start polling for progress updates
            $this->dispatch('start-loan-polling');

            Log::info('CSV Loan Import Started', [
                'batch_id' => $this->batchId,
                'batch_key' => $batchKey,
                'total_rows' => $this->totalRows,
                'total_jobs' => $this->totalJobs,
                'user_id' => auth('admin')->user()->id
            ]);

        } catch (\Exception $e) {
            $this->status = 'failed';
            $this->statusMessage = '❌ Error processing file: ' . $e->getMessage();
            
        }
    }

    #[On('check-loan-progress')]
    public function checkProgress()
    {
        if (!$this->batchId) {
            return;
        }

        try {
            // Retrieve the batch by ID
            $batch = Bus::findBatch($this->batchId);

            if (!$batch) return;

            $this->totalJobs = $batch->totalJobs;
            $this->completedJobs = $batch->processedJobs();
            $this->failedJobs = $batch->failedJobs;

            // Calculate progress
            $this->progress = $this->totalJobs > 0 ? 
                ($this->completedJobs / $this->totalJobs) * 100 : 0;

            // Get aggregated results from cache
            $batchKey = Cache::get("batch_key_{$this->batchId}");
            if ($batchKey) {
                $cachePrefix = "batch_results_{$batchKey}";

                $this->validRows = Cache::get("{$cachePrefix}_valid", 0);
                $this->invalidRows = Cache::get("{$cachePrefix}_invalid", 0);
                $errorCount = Cache::get("{$cachePrefix}_errors", 0);

                $this->processedRows = $this->validRows + $this->invalidRows;
                // Load recent errors from files instead of cache
                $this->loadRecentErrors($batchKey);

            }

            if ($batch->finished()) {
                $this->dispatch('stop-polling');
                
                if ($batch->hasFailures()) {
                    $this->status = 'completed';
                    $this->statusMessage = "Processing completed with some failures. {$this->completedJobs}/{$this->totalJobs} jobs succeeded.";
                } else {
                    $this->status = 'completed';
                    $this->statusMessage = "All jobs completed successfully! Processed {$this->processedRows} rows.";
                }

                // Generate download links
                if($batchKey)
                    $this->generateDownloadLinks($batchKey);
            } else {
                $this->status = 'processing';
                $this->statusMessage = "Processing... {$this->completedJobs}/{$this->totalJobs} jobs completed.";
            }
            
        } catch (\Exception $e) {
            $this->status = 'failed';
            $this->statusMessage = '❌ Error checking progress: ' . $e->getMessage();
            $this->dispatch('stop-loan-polling');
        }
    }

    private function loadRecentErrors($batchKey)
    {
        try {
            $errorFile = "batch_data/{$batchKey}_errors.json";
            
            if (Storage::exists($errorFile)) {
                $content = Storage::get($errorFile);
                if (!empty($content)) {
                    $errors = json_decode($content, true) ?? [];
                    
                    // Get last 10 errors for display
                    $recentErrors = array_slice($errors, -10);
                    
                    $this->processingErrors = array_map(function($error) {
                        return $error['error'] ?? 'Unknown error';
                    }, $recentErrors);
                } else {
                    $this->processingErrors = [];
                }
            } else {
                $this->processingErrors = [];
            }
        } catch (\Exception $e) {
            Log::error('Failed to load recent errors', [
                'batch_key' => $batchKey,
                'error' => $e->getMessage()
            ]);
            $this->processingErrors = [];
        }
    }


    private function generateDownloadLinks($batchKey)
    {
        if (!$batchKey) return;

        try {

            $this->downloadLinks = [];

            // Check for invalid rows file
            $invalidFile = "batch_data/{$batchKey}_invalid.json";
            if (Storage::exists($invalidFile)) {
                // Dispatch job to generate Excel report
                dispatch(new \App\Jobs\GenerateExcelReportJob(
                    'invalid',
                    $batchKey,
                    'Invalid Rows Report'
                ));
                $this->downloadLinks['invalid'] = route('download.excel', [
                    'type' => 'invalid',
                    'batch' => $batchKey
                ]);
            }else {
                Log::warning('Invalid rows file not found', [
                    'file_path' => $invalidFile,
                    'batch_key' => $batchKey
                ]);
            }

            // Check for errors file
            $errorFile = "batch_data/{$batchKey}_errors.json";
            if (Storage::exists($errorFile)) {
                // Dispatch job to generate Excel report
                dispatch(new \App\Jobs\GenerateExcelReportJob(
                    'errors',
                    $batchKey,
                    'Error Logs Report'
                ));
                $this->downloadLinks['errors'] = route('download.excel', [
                    'type' => 'errors',
                    'batch' => $batchKey
                ]);
            } else {
                Log::warning('Error logs file not found', [
                    'file_path' => $errorFile,
                    'batch_key' => $batchKey
                ]);
            }

            Log::info('Download links generated', [
                'batch_key' => $batchKey,
                'links' => $this->downloadLinks
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate download links: ' . $e->getMessage());
        }
    }

    public function resetUpload()
    {
        $this->reset([
            'csvFile', 'batchId', 'progress', 'status', 'statusMessage', 
            'processedRows', 'totalRows', 'validRows', 'invalidRows',
            'totalJobs', 'completedJobs', 'failedJobs', 'processingErrors', 'downloadLinks'
        ]);

        // Also reset the error bag
        $this->resetErrorBag();
    }

    public function processCsvRow(array $row, int $userId): ?array
    {
        $activeLoan = [];

        try {
            $validatedData = $this->validateRecord($row, $userId);

            if ($validatedData) {

                // Process active loans
                if($validatedData['status'] == 1) {
                    $activeLoan = $this->extractActiveLoan($validatedData, $userId);
                }

                LoanCapture::insert($validatedData);
                // Insert active loans if any
                if(!empty($activeLoan)) {
                    ActiveLoans::insert($activeLoan);
                }

                Log::debug('Record inserted successfully', [
                    'coop_id' => $validatedData['coopId'],
                    'affected_rows' => $validatedData
                ]);

                return $validatedData; // Return the new record to be inserted
            }

            // If the record is not valid, return null
            return null;

        } catch (\Exception $e) {
            Log::error('Error processing CSV row', [
                'row' => $row,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            
            throw $e; // Re-throw the exception to be handled by the job
        }

        

    }

    /**
     * Validate and transform the record before inserting into the database
     *
     * @param array $record
     * @param int $userId
     * @return array|null
     */
    protected function validateRecord(array $record, int $userId=1): ?array
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
                'userId' => $userId, // Retrieve admin id
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
            'userId' => $userId, // Retrieve admin id
        ];

    }

    /**
     * Extract active loan data from the validated record
     *
     * @param array $validatedRecord
     * @return array|null
     */
    protected function extractActiveLoan(array $validatedRecord, int $userId=1): ?array
    {
        return [
            'coopId' => $validatedRecord['coopId'],
            'loanAmount' => $validatedRecord['loanAmount'],
            'loanPaid' => 0,
            'loanBalance' => $validatedRecord['loanAmount'],
            'loanDate' => $validatedRecord['loanDate'],
            'repaymentDate' => $validatedRecord['repaymentDate'],
            'lastPaymentDate' => $validatedRecord['loanDate'],
            'userId' => $userId, // Retrieve admin id
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
