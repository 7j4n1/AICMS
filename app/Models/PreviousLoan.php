<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PreviousLoan extends Model
{
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';

    protected $fillable = [
        'coopId',
        'loanAmount',
        'loanDate',
        'guarantor1',
        'guarantor2',
        'guarantor3',
        'guarantor4',
        'status',
        'userId',
        'repaymentDate'
    ];

}
