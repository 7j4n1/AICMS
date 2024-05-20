<?php

namespace App\Imports;

use App\Models\PaymentCapture;
use Maatwebsite\Excel\Concerns\ToModel;

class PaymentsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new PaymentCapture([
            'coopId' => $row[0],
            'splitOption' => $row[1],
            'loanAmount' => $row[2],
            'savingAmount' => $row[3],
            'totalAmount' => $row[4],
            'paymentDate' => $row[5],
            'others' => $row[6],
            'shareAmount' => $row[7],
            'userId' => $row[8],
            'adminCharge' => $row[9]
        ]);
    }
}
