<?php

namespace App\Imports;

use App\Models\RepayCapture;
use Maatwebsite\Excel\Concerns\ToModel;

class RepayCaptureImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new RepayCapture([
            'id' => $row[0],
            'coopId' => $row[1],
            'item_capture_id' => $row[2],
            'amountToRepay' => $row[3],
            'loanBalance' => $row[4],
            'repaymentDate' => $row[5],
            'serviceCharge' => $row[6],
            'userId' => $row[7]
        ]);
    }
}
