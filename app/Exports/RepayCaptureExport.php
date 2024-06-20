<?php

namespace App\Exports;

use App\Models\RepayCapture;
use Maatwebsite\Excel\Concerns\FromCollection;

class RepayCaptureExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return RepayCapture::all();
    }
}
