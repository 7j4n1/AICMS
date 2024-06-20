<?php

namespace App\Imports;

use App\Models\ItemCapture;
use Maatwebsite\Excel\Concerns\ToModel;

class ItemCaptureImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ItemCapture([
            'id' => $row[0],
            'coopId' => $row[1],
            'quantity' => $row[2],
            'buyingDate' => $row[3],
            'payment_timeframe' => $row[4],
            'payment_status' => $row[5],
            'userId' => $row[6],
            'repaymentDate' => $row[7],
            'category_id' => $row[8],
            'loanPaid' => $row[9],
            'loanBalance' => $row[10]
        ]);
    }
}
