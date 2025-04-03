<?php

namespace App\Livewire\Utils;

use App\Models\Member;
use League\Csv\Reader;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImportMemberCsv extends Component
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
        Log::info("CSV import completed: {$totalRows} records processed.");

        // Dispatch completion event
        $this->dispatch('show-notification', [
            'type' => 'success',
            'message' => "CSV import completed! Processed {$totalRows} records.",
        ]);

        session()->flash('success', "Import completed successfully! Processed {$totalRows} records.");
        
        // Reset the component state
        $this->reset(['isProcessing', 'uploadedChunks', 'totalChunks', 'progress']);
    }

    // public function handleChunkUploaded($chunkNumber, $totalChunks, $path)
    // {
    //     $this->uploadedChunks = $chunkNumber;
    //     $this->totalChunks = $totalChunks;

    //     // If all chunks are uploaded, start processing
    //     if ($this->uploadedChunks == $this->totalChunks) {
    //         $this->processUploadedChunks();
    //     }
        
    // }

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

    // public function processUploadedChunks()
    // {
    //     $this->isProcessing = true;
    //     $this->errorMessage = '';


    //     DB::beginTransaction();
    //     try {

    //         // Log the start of processing
    //         Log::info('Processing uploaded chunks...');

    //         // Get all uploaded chunks from storage
    //         $chunkFiles = Storage::files('chunks');
    //         $totalFiles = count($chunkFiles);
    //         $processedFiles = 0;

    //         foreach ($chunkFiles as $chunkPath) {
    //             $processedFiles++;

    //             // Calculate progress
    //             $progress = round(($processedFiles / $totalFiles) * 100);

    //             // Dispatch progress to the frontend
    //             $this->sendDispatchEvent([
    //                 'isProcessing' => true,
    //                 'progress' => $progress,
    //                 'message' => "Processing chunk {$processedFiles} of {$totalFiles}...",
    //             ]);

    //             // process the chunk file
    //             $this->processChunkFile($chunkPath);

    //             // Log the chunk processing
    //             Log::info('Processed chunk file: ' . $chunkPath);
    //             // dispatch progress to the frontend

    //             // Allow some time for the UI to update
    //             // This is a workaround to ensure the UI updates before the next chunk is processed
    //             // usleep(500000); // 50 milliseconds pause
                
    //         }

    //         DB::commit();
    //         $this->isProcessing = false;
    //         $this->isCompleted = true;
            
            
    //         // Log the success message
    //         Log::info('All chunks processed successfully.');

    //         // $this->reset(['isProcessing', 'uploadedChunks', 'totalChunks']);

    //         session()->flash('success', 'All chunks processed successfully.');

    //         // $this->sendDispatchEvent();

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         $this->errorMessage = 'Processing failed: ' . $e->getMessage();
    //         $this->isProcessing = false;

    //         // Dispatch error message
    //         // $this->sendDispatchEvent([
    //         //     'isProcessing' => false,
    //         //     'isCompleted' => false,
    //         //     'progress' => 0,
    //         //     'message' => $this->errorMessage,
    //         //     'error' => true
    //         // ]);

    //         session()->flash('error', $this->errorMessage);
    //         // Log the error message
    //         Log::error('Error processing chunks: ' . $this->errorMessage);
    //         // log the exception
    //         Log::error('Exception: ', [
    //             'exception' => $e,
    //         ]);
    //     }
    // }

    protected function processChunkFile($chunkPath)
    {
        $fullPath = storage_path('app/' . $chunkPath);

        // use league csv for efficient processing
        $reader = Reader::createFromPath($fullPath, 'r');
        $reader->setDelimiter(','); // Set the delimiter to comma
        $reader->setHeaderOffset(null); // Set the header offset to 0

        $batchSize = 500; // Number of records to process at once
        $batch = [];

        // \Log::info("Processing chunk file: " . $chunkPath);
        // Read the CSV file and process each record
        foreach ($reader->getRecords() as $record) {
            // \Log::info("Processing record: ", $record);
            // validate and transform the record
            if(!isset($record[0]) || !isset($record[1]) || ($record[0] == 'COOP')) {
                Log::warning("Skipping record due to missing required fields: ", $record);
                continue; // Skip if required fields are missing
            }
            $validatedData = $this->validateRecord($record);

            if ($validatedData) {
                $batch[] = $validatedData;
            }

            // If batch size is reached, insert into the database
            // If batch size is reached, insert into the database
            if (count($batch) >= $batchSize) {
                DB::table('members')->insert($batch);
                $batch = []; // Reset the batch
            }
        }

        // Insert any remaining records in the batch
        if (!empty($batch)) {
            DB::table('members')->insert($batch);
        }

        // clean up the chunk file
        unlink($fullPath);
    }

    protected function validateRecord(array $record): ?array
    {
        // Perform your validation and transformation here
        $uniqueId = $record[0]; // assuming the first column is uniqueId
        // Check if record already exists
        $existingRecord = Member::where('coopId', $uniqueId)->first();
        if ($existingRecord) {
            // update the existing record
            $existingRecord->update([
                'surname' => $this->setNullIfEmpty($record[1]),
                'otherNames' => $this->setNullIfEmpty($record[2]),
                'occupation' => $this->setNullIfEmpty($record[3]),
                'gender' => $this->setNullIfEmpty($record[4]),
                'religion' => $this->setNullIfEmpty($record[5]),
                'phoneNumber' => $this->setNullIfEmpty($record[6]),
                'accountNumber' => $this->setNullIfEmpty($record[7]),
                'bankName' => $this->setNullIfEmpty($record[8]),
                'nextOfKinName' => $this->setNullIfEmpty($record[9]),
                'nextOfKinPhoneNumber' => $this->setNullIfEmpty($record[10]),
                'yearJoined' => $this->setNullIfEmpty($record[11]),
                'userId' => auth('admin')->user()->id, // Retrieve admin id
            ]);
            return null; // Skip this record if it already exists
        }

        if(!isset($record[0]) || !isset($record[1])) {
            return null; // Skip if required fields are missing
        }

        return [
            'coopId' => $record[0],
            'surname' => $this->setNullIfEmpty($record[1]),
            'otherNames' => $this->setNullIfEmpty($record[2]),
            'occupation' => $this->setNullIfEmpty($record[3]),
            'gender' => $this->setNullIfEmpty($record[4]),
            'religion' => $this->setNullIfEmpty($record[5]),
            'phoneNumber' => $this->setNullIfEmpty($record[6]),
            'accountNumber' => $this->setNullIfEmpty($record[7]),
            'bankName' => $this->setNullIfEmpty($record[8]),
            'nextOfKinName' => $this->setNullIfEmpty($record[9]),
            'nextOfKinPhoneNumber' => $this->setNullIfEmpty($record[10]),
            'yearJoined' => $this->setNullIfEmpty($record[11]),
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

    // This method exists just to force a refresh
    public function handleProcessingStatus()
    {
        // This method is intentionally left empty
        // It is used to force a refresh of the component
        // when the processing status changes
    }
    

    public function render()
    {
        return view('livewire.utils.import-member-csv');
    }

    public function sendDispatchEvent($array = [])
    {
        $this->dispatch('processing-status-changed', $array);
    }
}
