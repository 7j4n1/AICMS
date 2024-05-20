<?php

namespace App\Exports;

use App\Models\ActiveLoans;
use Maatwebsite\Excel\Concerns\FromCollection;

class ActiveLoansExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ActiveLoans::all();
    }
}
