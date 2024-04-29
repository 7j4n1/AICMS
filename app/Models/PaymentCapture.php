<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentCapture extends Model
{
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';

    protected $primaryKey = "id";

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

    protected $casts = [
        'id' => 'string',
    ];
}
