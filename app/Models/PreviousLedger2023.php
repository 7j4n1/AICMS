<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PreviousLedger2023 extends Model
{
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';

    protected $fillable = [
        'coopId',
        'splitOption',
        'loanAmount',
        'savingAmount',
        'totalAmount',
        'paymentDate',
        'others',
        'shareAmount',
        'userId',
        'adminCharge'
    ];
}
