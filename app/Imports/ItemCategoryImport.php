<?php

namespace App\Imports;

use App\Models\ItemCategory;
use Maatwebsite\Excel\Concerns\ToModel;

class ItemCategoryImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ItemCategory([
            'id' => $row[0],
            'name' => $row[1],
            'price' => $row[2]
        ]);
    }
}
