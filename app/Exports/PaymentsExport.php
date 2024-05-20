<?php

namespace App\Exports;

use App\Models\PaymentCapture;
use Maatwebsite\Excel\Concerns\FromCollection;

class PaymentsExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return PaymentCapture::all();
    }
}
