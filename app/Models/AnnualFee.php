<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'coopId',
        'annual_savings',
        'annual_fee',
        'total_savings',
        'userId',
        'status',
        'annual_year',
        'editDates',
        'editedBy'
    ];

    protected $casts = [
        'editDates' => 'array',
        'editedBy' => 'array'
    ];

    // update the edit dates
    public function updateEditDates()
    {
        // get the current edit dates or initialize an empty array
        $dates = $this->editDates ?? [];

        // add the current date to the beginning of the array
        array_unshift($dates, now());

        // keep only the last 3 edit dates
        $this->editDates = array_slice($dates, 0, 3);

        // get the current edited by or initialize an empty array
        $editedBy = $this->editedBy ?? [];

        // add the current user to the beginning of the array
        array_unshift($editedBy, auth('admin')->user()->name);

        // keep only the last 3 edited by
        $this->editedBy = array_slice($editedBy, 0, 3);
    }
}
