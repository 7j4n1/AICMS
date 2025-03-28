<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentNotificationUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'coopId',
        'amount',
        'payment_date',
        'payment_time',
        'bank_used',
        'payment_channel',
        'depositor_name',
        'reference_number',
        'additional_details',
        'status',
        'evidence_path',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejected_reason',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'coopId', 'coopId');
    }
}
