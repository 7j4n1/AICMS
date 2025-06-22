<?php

namespace App\Jobs;

use Illuminate\Support\Str;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class RunCsvImportJob implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 300;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     */
    public $maxExceptions = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $chunkRecord,
        public string $batchKey,
        public string $handlerClass,
        public int $userId
    )
    {
        // Set the queue name based on the handler class
        $this->onQueue($this->getQueueName());
    }

    /**
     * Get queue name based on handler class
     */
    private function getQueueName(): string
    {
        if (str_contains($this->handlerClass, 'ImportMemberCsv')) {
            return 'member-imports';
        } elseif (str_contains($this->handlerClass, 'ImportLoansCsv')) {
            return 'loan-imports';
        } elseif (str_contains($this->handlerClass, 'ImportLedgerCsv')) {
            return 'ledger-imports';
        }
        
        return 'csv-imports'; // Default queue
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return $this->batchKey . '_' . md5(serialize($this->chunkRecord) . Str::uuid());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            Log::info('Job cancelled - batch was cancelled', [
                'batch_key' => $this->batchKey,
                'handler' => class_basename($this->handlerClass)
            ]);
            return;
        }

        // Create handler instance with better error handling
        try {
            $handler = App::make($this->handlerClass);
            
            // Verify the handler has the required method
            if (!method_exists($handler, 'processCsvRow')) {
                throw new \Exception("Handler {$this->handlerClass} does not have processCsvRow method");
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to create or validate handler', [
                'handler_class' => $this->handlerClass,
                'error' => $e->getMessage(),
                'batch_key' => $this->batchKey
            ]);
            throw $e;
        }

        $records = $this->chunkRecord;
        // convert each row from csv string to an array

        // $valid = [];
        $invalid = [];
        $errors = [];
        $validCount = 0;
        $invalidCount = 0;
        $errorCount = 0;

        
        try {
            // Start a database transaction
            foreach ($records as $rowIndex => $row) {
                
                Log::info('Processing CSV Row', [
                    'row' => $row,
                    'user_id' => $this->userId,
                ]);
                // remove any leading or trailing whitespace
                $row = trim($row);
                $arrayRow = str_getcsv($row); // convert the CSV string to an array

                // if the entire row is wrapped in quotes, remove the quotes
                if (count($arrayRow) === 1 && strpos($arrayRow[0], ',') !== false) {
                    $arrayRow = str_getcsv($arrayRow[0]);
                }
                // Process the row using the handler
                try {
                    $result = $handler->processCsvRow($arrayRow, $this->userId);
                    if ($result && is_array($result) && count($result) > 0 ) {
                        // New record to insert
                        $validCount++;
                        
                        Log::debug('Record inserted', [
                            'coop_id' => $result['coopId'] ?? 'unknown',
                            'row_index' => $rowIndex,
                        ]);
                        
                    } else if(is_array($result) && count($result) === 0) {
                        // Record was updated
                        $validCount++;

                        Log::debug('Record updated');
                
                    }else {
                        // If the result is empty or not an array, consider it invalid
                        
                        $invalidCount++;
                        $invalid[] = [
                            'row_index' => $rowIndex + 1,
                            'raw_data' => $row,
                            'parsed_data' => $arrayRow,
                            'reason' => 'Invalid or incomplete data',
                            'timestamp' => now()->toISOString()
                        ];
                        
                        Log::debug('Invalid record', ['row_index' => $rowIndex]);
                    }

                } catch (\Exception $e) {
                    Log::error('CSV Row Processing Error', [
                        'row_index' => $rowIndex,
                        'error' => $e->getMessage(),
                        'user_id' => $this->userId,
                    ]);
                    
                    $invalidCount++;
                    $errorCount++;
                    
                    // Store error details
                    $errors[] = [
                        'row_index' => $rowIndex + 1,
                        'raw_data' => $row,
                        'parsed_data' => $arrayRow,
                        'error' => $e->getMessage(),
                        'timestamp' => now()->toISOString()
                    ];
                }

            }

            Log::info('CSV chunk processed successfully', [
                'batch_key' => $this->batchKey,
                'valid_count' => $validCount,
                'invalid_count' => $invalidCount,
                'error_count' => $errorCount,
                'user_id' => $this->userId,
            ]);

            // Update only counters in cache
            $this->updateProgress($validCount, $invalidCount, $errorCount);

            // Store problematic rows in files (scalable)
            $this->appendToFiles($invalid, $errors);

        } catch (\Throwable $th) {
            
            Log::error('CSV Import Job Error: ' . $th->getMessage(), [
                'user_id' => $this->userId,
                'batch_key' => $this->batchKey,
                'trace' => $th->getTraceAsString(),
            ]);

            throw $th;
            
        }
        

    }

    private function updateProgress(int $validCount, int $invalidCount, int $errorCount): void
    {
        // Use simple atomic increment operations - much more efficient
        $cachePrefix = "batch_results_{$this->batchKey}";

        // Initialize cache keys if they don't exist, then increment
        $validKey = "{$cachePrefix}_valid";
        $invalidKey = "{$cachePrefix}_invalid";
        $errorsKey = "{$cachePrefix}_errors";
        
        // Get current values or initialize to 0
        $currentValid = Cache::get($validKey, 0);
        $currentInvalid = Cache::get($invalidKey, 0);
        $currentErrors = Cache::get($errorsKey, 0);
        
        // Add the new counts
        $newValid = $currentValid + $validCount;
        $newInvalid = $currentInvalid + $invalidCount;
        $newErrors = $currentErrors + $errorCount;
        
        // Store the updated values with expiration
        Cache::put($validKey, $newValid, now()->addHours(12));
        Cache::put($invalidKey, $newInvalid, now()->addHours(12));
        Cache::put($errorsKey, $newErrors, now()->addHours(12));
        
        // Set expiration marker
        Cache::put("{$cachePrefix}_exp", true, now()->addHours(12));

        Log::info('Progress updated', [
            'batch_key' => $this->batchKey,
            'chunk_valid' => $validCount,
            'chunk_invalid' => $invalidCount,
            'chunk_errors' => $errorCount,
            'total_valid' => $newValid,
            'total_invalid' => $newInvalid,
            'total_errors' => $newErrors,
        ]);

    }

    private function appendToFiles(array $invalidRows, array $errors): void
    {
        try {
            // Append invalid rows to file if any
            if (!empty($invalidRows)) {
                $this->appendToJsonFile("batch_data/{$this->batchKey}_invalid.json", $invalidRows);
            }
            
            // Append errors to file if any
            if (!empty($errors)) {
                $this->appendToJsonFile("batch_data/{$this->batchKey}_errors.json", $errors);
            }
            
        } catch (\Exception $e) {
            Log::error('Failed to append to files', [
                'batch_key' => $this->batchKey,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function appendToJsonFile(string $filePath, array $newData): void
    {
        // Create a lock file to prevent concurrent writes
        $lockFile = $filePath . '.lock';
        $maxRetries = 5;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                // Try to create lock file atomically
                if (Storage::missing($lockFile)) {
                    Storage::put($lockFile, time());
                    
                    // Ensure directory exists
                    Storage::makeDirectory(dirname($filePath));
                    
                    // Read existing data
                    $existingData = [];
                    if (Storage::exists($filePath)) {
                        $content = Storage::get($filePath);
                        if (!empty($content)) {
                            $existingData = json_decode($content, true) ?? [];
                        }
                    }
                    
                    // Append new data
                    $allData = array_merge($existingData, $newData);
                    
                    // Write back to file
                    Storage::put($filePath, json_encode($allData));
                    
                    // Remove lock
                    Storage::delete($lockFile);
                    
                    Log::debug('Successfully appended to file', [
                        'file' => $filePath,
                        'new_items' => count($newData),
                        'total_items' => count($allData)
                    ]);
                    
                    return; // Success, exit
                }
                
                // Lock exists, wait and retry
                $attempt++;
                usleep(100000); // Wait 100ms
                
            } catch (\Exception $e) {
                // Clean up lock on error
                Storage::delete($lockFile);
                throw $e;
            }
        }
        
        // If we reach here, we couldn't get the lock
        throw new \Exception("Could not acquire file lock after {$maxRetries} attempts");
    }

    // private function currentBatchReportDetails(array $valid, array $invalid, array $errors): void
    // {
    //     $lockKey = "batch_details_lock_{$this->batchKey}";
    //     $cacheKey = "batch_all_results_{$this->batchKey}";
        
    //     $lock = Cache::lock($lockKey, 10); // Lock for 30 seconds

    //     try {
    //         if($lock->get()) {
    //             $allResults = Cache::get($cacheKey, [
    //                 'valid_data' => [],
    //                 'invalid_data' => [],
    //                 'error_logs' => []
    //             ]);

    //             $allResults['valid_data'] = array_merge($allResults['valid_data'], $valid);
    //             $allResults['invalid_data'] = array_merge($allResults['invalid_data'], $invalid);
    //             $allResults['error_logs'] = array_merge($allResults['error_logs'], $errors);
                
    //             // Store the results in cache for 12 hours
    //             // This will be used to generate the final report
    //             // after the batch is completed
    //             Cache::put($cacheKey, $allResults, now()->addHours(12));

    //             // Store the updated results in cache
    //             Log::info('Storing all results in cache', [
    //                 'batch_key' => $this->batchKey,
    //                 'valid_count' => count($allResults['valid_data']),
    //                 'invalid_count' => count($allResults['invalid_data']),
    //                 'error_count' => count($allResults['error_logs']),
    //             ]);

    //         } else {
    //             Log::warning('Could not acquire lock for batch details', [
    //                 'batch_key' => $this->batchKey,
    //             ]);
    //         }        
    //     } finally {
    //         $lock->release(); // Release the lock
    //     }
            
    // }
}
