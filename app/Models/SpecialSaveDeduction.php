<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialSaveDeduction extends Model
{
    use HasFactory;

    protected $table = 'special_save_deductions';
    
    protected $fillable = [
        'coopId',
        'type',
        'credit',
        'debit',
        'balance',
        'paymentDate',
        'userId',
        'editDates',
        'editedBy'
    ];
    protected $casts = [
        'editDates' => 'array',
        'editedBy' => 'array',
    ];
    
}
