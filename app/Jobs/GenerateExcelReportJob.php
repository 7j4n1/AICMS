<?php

namespace App\Jobs;

use Illuminate\Support\Str;
use App\Exports\SheetExport;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class GenerateExcelReportJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        public string $type,
        public string $batchKey,
        public string $title,
    )
    {
        $this->onQueue('excel-reports'.$this->batchKey.Str::uuid()); // Set the queue for this job
    }

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return $this->batchKey . '_' . md5(serialize($this->title) . Str::uuid());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $filename = "csv_import_{$this->type}_{$this->batchKey}.xlsx";
            $path = "reports/{$filename}";

            // Ensure reports directory exists
            Storage::disk('public')->makeDirectory('reports');

            // Load data from files instead of passing in constructor
            $data = $this->loadDataFromFile();

            if (empty($data)) {
                Log::info("No data found for report generation", [
                    'type' => $this->type,
                    'batch_key' => $this->batchKey,
                ]);
                return; // Skip if no data
            }

            // Prepare data with headers based on type
            $exportData = $this->prepareDataForExport($data);

            // Create Excel file using SheetExport
            Excel::store(
                new SheetExport($exportData, $this->title),
                $path,
                'public'
            );

            // Verify file was created
            if (Storage::disk('public')->exists($path)) {
                // Store the file path in cache for download
                $cacheKey = "excel_report_{$this->type}_{$this->batchKey}";
                Cache::put($cacheKey, $path, now()->addHours(12));

                Log::info("Excel report generated successfully", [
                    'type' => $this->type,
                    'batch_key' => $this->batchKey,
                    'path' => $path,
                    'rows_count' => count($data),
                    'file_size' => Storage::disk('public')->size($path)
                ]);
            } else {
                throw new \Exception("File was not created: {$path}");
            }


        } catch (\Exception $e) {
            Log::error("Error generating Excel report", [
                'type' => $this->type,
                'batch_key' => $this->batchKey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e; // Re-throw the exception to handle it in the queue
        }
    }

    /**
     * Load data from JSON files based on type
     */
    private function loadDataFromFile(): array
    {
        $filePath = "batch_data/{$this->batchKey}_{$this->type}.json";
        
        if (!Storage::exists($filePath)) {
            Log::warning("Data file not found", [
                'file_path' => $filePath,
                'type' => $this->type,
                'batch_key' => $this->batchKey
            ]);
            return [];
        }

        try {
            $content = Storage::get($filePath);
            if (empty($content)) {
                return [];
            }

            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("JSON decode error: " . json_last_error_msg());
            }

            return $data ?? [];

        } catch (\Exception $e) {
            Log::error("Failed to load data from file", [
                'file_path' => $filePath,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Prepare data for export based on type.
     */
    private function prepareDataForExport(array $data): array
    {
        if (empty($data)) {
            return [['No data available']];
        }

        switch ($this->type) {
            case 'invalid':
                return $this->prepareInvalidRowsData($data);
            case 'errors':
                return $this->prepareErrorLogsData($data);
            default:
                return [['Unsupported report type']];
        }
    }

    // private function prepareValidRowsData($data): array
    // {
    //     $headers = [
    //         'Coop ID', 'Surname', 'Other Names', 'Occupation', 'Gender', 
    //         'Religion', 'Phone Number', 'Account Number', 'Bank Name', 
    //         'Next of Kin Name', 'Next of Kin Phone', 'Year Joined', 'User ID'
    //     ];

    //     $data = [$headers];

    //     foreach ($this->data as $row) {
    //         if (is_array($row)) {
    //             $data[] = [
    //                 $row['coopId'] ?? '',
    //                 $row['surname'] ?? '',
    //                 $row['otherNames'] ?? '',
    //                 $row['occupation'] ?? '',
    //                 $row['gender'] ?? '',
    //                 $row['religion'] ?? '',
    //                 $row['phoneNumber'] ?? '',
    //                 $row['accountNumber'] ?? '',
    //                 $row['bankName'] ?? '',
    //                 $row['nextOfKinName'] ?? '',
    //                 $row['nextOfKinPhoneNumber'] ?? '',
    //                 $row['yearJoined'] ?? '',
    //                 $row['userId'] ?? ''
    //             ];
    //         }
    //     }

    //     return $data;
    // }

    private function prepareInvalidRowsData(array $data): array
    {
        $headers = ['Row Index', 'Raw Data', 'Parsed Data', 'Reason', 'Timestamp'];
        $rows = [$headers];

        foreach ($data as $item) {
            $rows[] = [
                $item['row_index'] ?? '',
                $item['raw_data'] ?? '',
                is_array($item['parsed_data'] ?? []) ? implode(' | ', $item['parsed_data']) : '',
                $item['reason'] ?? 'Invalid or incomplete data',
                $item['timestamp'] ?? ''
            ];
        }

        return $rows;
    }

    private function prepareErrorLogsData(array $data): array
    {
        $headers = ['Row Index', 'Raw Data', 'Parsed Data', 'Error Message', 'Timestamp'];
        $rows = [$headers];

        foreach ($data as $item) {
            $rows[] = [
                $item['row_index'] ?? '',
                $item['raw_data'] ?? '',
                is_array($item['parsed_data'] ?? []) ? implode(' | ', $item['parsed_data']) : '',
                $item['error'] ?? 'Unknown error',
                $item['timestamp'] ?? ''
            ];
        }

        return $rows;
    }
}
