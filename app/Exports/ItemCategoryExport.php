<?php

namespace App\Exports;

use App\Models\ItemCategory;
use Maatwebsite\Excel\Concerns\FromCollection;

class ItemCategoryExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ItemCategory::all();
    }
}
