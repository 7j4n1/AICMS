<?php

namespace App\Traits;


trait LivewireCsvImporter
{
    public $csvFile;

    // Must be defined in the using component:
    abstract public function processCsvRow(array $row, int $userId): ?array;
}