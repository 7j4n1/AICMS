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

    protected $listeners = [
        'chunk-uploaded' => 'handleChunkUploaded',
    ];

    public function handleChunkUploaded($chunkNumber, $totalChunks)
    {
        $this->uploadedChunks = $chunkNumber;
        $this->totalChunks = $totalChunks;

        // If all chunks are uploaded, start processing
        if ($this->uploadedChunks == $this->totalChunks) {
            $this->processUploadedChunks();
        }
        
    }

    public function processUploadedChunks()
    {
        $this->isProcessing = true;
        $this->errorMessage = '';
        DB::beginTransaction();
        try {
            // Get all uploaded chunks from storage
            $chunkFiles = Storage::files('chunks');

            foreach ($chunkFiles as $chunkPath) {
                $this->processChunkFile($chunkPath);

                // Log the chunk processing
                Log::info('Processed chunk file: ' . $chunkPath);
                // dispatch progress to the frontend
                
            }

            DB::commit();
            $this->isProcessing = false;
            
            

            session()->flash('success', 'All chunks processed successfully.');
            // Log the success message
            Log::info('All chunks processed successfully.');

            $this->reset(['isProcessing', 'uploadedChunks', 'totalChunks']);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = 'Processing failed: ' . $e->getMessage();
            $this->isProcessing = false;
            session()->flash('error', $this->errorMessage);
            // Log the error message
            Log::error('Error processing chunks: ' . $this->errorMessage);
            // log the exception
            Log::error('Exception: ', [
                'exception' => $e,
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

    public function render()
    {
        return view('livewire.utils.import-member-csv');
    }
}
