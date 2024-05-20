<?php

namespace App\Imports;

use App\Models\ActiveLoans;
use Maatwebsite\Excel\Concerns\ToModel;

class ActiveLoansImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ActiveLoans([
            // fill the columns with the data from the excel file
            'coopId' => $row[0],
            'loanAmount' => $row[1],
            'loanPaid' => $row[2],
            'loanBalance' => $row[3],
            'userId' => $row[4],
            'loanDate' => $row[5],
            'repaymentDate' => $row[6],
            'lastPaymentDate' => $row[7]
        ]);
    }
}
