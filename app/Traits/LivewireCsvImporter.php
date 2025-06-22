<?php

namespace App\Traits;

use App\Jobs\RunCsvImportJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

trait LivewireCsvImporter
{
    public $csvFile;

    // public function updatedCsv()
    // {
    //     $this->progressKey = 'csv-import-' . uniqid();
    //     $path = $this->csv->store('temp');
    //     RunCsvImportJob::dispatch(
    //         $path,
    //         $this->progressKey,
    //         static::class,
    //         auth('admin')->user()->id
    //     );
    //     $this->dispatch('toast', message: '✅ Import started...');
    // }


    // Must be defined in the using component:
    abstract public function processCsvRow(array $row, int $userId): ?array;
}