<?php

namespace App\Exports;

use App\Models\ItemCapture;
use Maatwebsite\Excel\Concerns\FromCollection;

class ItemCaptureExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ItemCapture::all();
    }
}
