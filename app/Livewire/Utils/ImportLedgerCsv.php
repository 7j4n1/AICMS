<?php

namespace App\Livewire\Utils;

use App\Models\Member;
use League\Csv\Reader;
use Livewire\Component;
use Illuminate\Bus\Batch;
use App\Models\ActiveLoans;
use App\Models\LoanCapture;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use App\Jobs\RunCsvImportJob;
use Livewire\WithFileUploads;
use App\Models\PaymentCapture;
use Illuminate\Support\Facades\DB;
use App\Traits\LivewireCsvImporter;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use App\Models\SpecialSaveDeduction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ImportLedgerCsv extends Component
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
                ->name("Import Ledger CSV processing - {$batchKey}")
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
            $this->dispatch('start-ledger-polling');

            Log::info('CSV Ledger Import Started', [
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

    #[On('check-ledger-progress')]
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
                $this->dispatch('stop-ledger-polling');
                
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
            $this->dispatch('stop-polling');
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
        $loanUpdatesBatch = [];
        $specialSaveBatch = [];

        try {
            $validatedData = $this->validateRecord($row, $userId);

            if ($validatedData) {
                
                // Track if the record is a loan update
                if($validatedData['loanAmount'] > 0) {
                    $loanUpdatesBatch = [
                        'coopId' => $validatedData['coopId'],
                        'loanAmount' => $validatedData['loanAmount'],
                        'paymentDate' => $validatedData['paymentDate'],
                    ];
                }

                if($validatedData['others'] > 0) {
                    $specialSaveBatch = [
                        'coopId' => $validatedData['coopId'],
                        'paymentDate' => $validatedData['paymentDate'],
                        'debit' => 0,
                        'type' => 'special',
                        'credit' => $validatedData['others'],
                    ];
                }

                PaymentCapture::insert($validatedData);

                if(!empty($specialSaveBatch)) {
                    SpecialSaveDeduction::insert($specialSaveBatch);
                }
                
                if(!empty($loanUpdatesBatch)) {
                    $this->processLoanUpdatesInBatch($loanUpdatesBatch);
                }
                

                return $validatedData; // Return the validated data for further processing if needed
                
            } 

            return null; // Return null if the record is invalid or should be skipped

        } catch (\Exception $th) {
            Log::error('Failed to upsert record', [
                'coop_id' => $row[0] ?? 'N/A',
                'error' => $th->getMessage(),
                'data' => $row            
            ]);

            throw $th; // Re-throw the exception to be handled by the job
        }

        
        
    }

    /**
     * Validate and transform the record
     *
     * @param array $record
     * @return array|null
     */
    protected function validateRecord(array $record, int $userId): ?array
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
            'userId' => $userId, // Retrieve admin id
            'adminCharge' => 0,
            'totalAmount' => $totalSum,
            'paymentDate' => $paymentDate,
        ];

    }

    // protected function processLoanUpdatesInBatch(array $loanUpdate)
    // {
    //     if(empty($loanUpdate)) {
    //         return;
    //     }

    //     // Process the loan updates in batch
    //     //group by coopId to avoid duplicate updates
    //     $groupedUpdates = [];
    //     foreach ($loanUpdate as $update) {
    //         $coopId = $update['coopId'];

    //         if (!isset($groupedUpdates[$coopId])) {
    //             $groupedUpdates[$coopId] = [
    //                 'totalPayment' => 0,
    //                 'latestDate' => null
    //             ];
    //         }

    //         $groupedUpdates[$coopId]['totalPayment'] += $update['loanAmount'];

    //         // Track the latest payment date
    //         $currentDate = strtotime($update['paymentDate']);
    //         if (!$groupedUpdates[$coopId]['latestDate'] || 
    //             $currentDate > strtotime($groupedUpdates[$coopId]['latestDate'] ?? '')) {
    //             $groupedUpdates[$coopId]['latestDate'] = $update['paymentDate'];
    //         }
    //     }

    //     // fetch all active loans for these coopIds
    //     $coopIds = array_keys($groupedUpdates);
    //     $activeLoans = ActiveLoans::whereIn('coopId', $coopIds)->get()->keyBy('coopId');

    //     $bulkFullyPaid = [];
    //     $bulkPartiallyPaid = [];

    //     foreach ($groupedUpdates as $coopId => $update) {
    //         if(!isset($activeLoans[$coopId])) {
    //             continue; // Skip if no active loan found
    //         }

    //         $activeLoan = $activeLoans[$coopId];
    //         $totalPayment = $update['totalPayment'];
    //         $latestDate = $update['latestDate'];

    //         if($totalPayment >= $activeLoan->loanBalance) {
    //             // loan is fully paid
    //             $bulkFullyPaid[] = $activeLoan->coopId;
    //         } else {
    //             // loan is partially paid, need to update the balance individually
    //             $bulkPartiallyPaid[] = [
    //                 'loan' => $activeLoan,
    //                 'payment' => $totalPayment,
    //                 'date' => $latestDate,
    //             ];
    //         }
    //     }

    //     // update the active loans in bulk
    //     if(!empty($bulkFullyPaid)) {

    //         // Move to completed loans table and
    //         // active loans will be deleted automatically
    //         LoanCapture::whereIn('coopId', $bulkFullyPaid)
    //             ->get()
    //             ->each(function ($loan) {
    //                 $loan->completedLoan();
    //             });
        
    //     }

    //     // update the active loans individually for partially paid
    //     foreach ($bulkPartiallyPaid as $update) {
    //         $payment = $update['payment'];
    //         $date = $update['date'];

    //         // update the loan balance
    //         $update['loan']->setPayment($payment, $date);
    //     }
    // }

    protected function processLoanUpdatesInBatch(array $loanUpdate)
    {
        if(empty($loanUpdate)) {
            return;
        }

        // Since we're passing a single loan update, not an array of updates
        // We need to handle it as a single update
        $coopId = $loanUpdate['coopId'];
        $loanAmount = $loanUpdate['loanAmount'];
        $paymentDate = $loanUpdate['paymentDate'];

        Log::debug('Processing single loan update', [
            'coop_id' => $coopId,
            'loan_amount' => $loanAmount,
            'payment_date' => $paymentDate
        ]);

        // Fetch the active loan for this coopId
        $activeLoan = ActiveLoans::where('coopId', $coopId)->first();

        if (!$activeLoan) {
            Log::warning('No active loan found for coopId', ['coop_id' => $coopId]);
            return; // Skip if no active loan found
        }

        try {
            if ($loanAmount >= $activeLoan->loanBalance) {
                // Loan is fully paid - move to completed loans
                $loanCapture = LoanCapture::where('coopId', $coopId)->first();
                if ($loanCapture) {
                    $loanCapture->completedLoan();
                    Log::info('Loan marked as completed', ['coop_id' => $coopId]);
                }
            } else {
                // Loan is partially paid - update the balance
                $activeLoan->setPayment($loanAmount, $paymentDate);
                Log::info('Loan balance updated', [
                    'coop_id' => $coopId,
                    'payment' => $loanAmount,
                    'new_balance' => $activeLoan->loanBalance - $loanAmount
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error processing loan update', [
                'coop_id' => $coopId,
                'error' => $e->getMessage(),
                'loan_amount' => $loanAmount
            ]);
            throw $e;
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
