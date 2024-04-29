<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompletedLoans extends Model
{
    use HasFactory;

    protected $fillable = [
        'coopId',
        'loanAmount',
        'loanPaid',
        'loanBalance',
        'userId',
        'loanDate',
        'repaymentDate',
        'lastPaymentDate',
    ];
}
