<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemCapture extends Model
{
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';

    protected $fillable = [
        'coopId',
        'quantity',
        'buyingDate',
        'payment_timeframe',
        'payment_status',
        'loan_type',
        'userId',
        'repaymentDate',
        'category_id',
        'loanPaid',
        'loanBalance'
    ];

    public function category()
    {
        return $this->belongsTo(ItemCategory::class,'category_id');
    }

    // has many repayments
    public function repayments()
    {
        return $this->hasMany(RepayCapture::class,'item_capture_id', 'id');
    }

    // member
    public function member()
    {
        return $this->belongsTo(Member::class, 'coopId', 'coopId');
    }
}
