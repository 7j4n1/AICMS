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
        'annual_year'
    ];
}
