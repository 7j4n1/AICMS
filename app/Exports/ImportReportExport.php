<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ImportReportExport implements WithMultipleSheets
{
    protected $valid, $invalid, $errors;

    public function __construct($valid, $invalid, $errors) 
    {
        $this->valid = $valid;
        $this->invalid = $invalid;
        $this->errors = $errors;
    }

    public function sheets(): array
    {
        return [
            new SheetExport($this->valid, 'Valid Rows'),
            new SheetExport($this->invalid, 'Invalid Rows'),
            new SheetExport($this->errors, 'Error Log'),
        ];
    }
}