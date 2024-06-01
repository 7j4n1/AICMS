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
        'category_id',
        'quantity',
        'buyingDate',
        'payment_timeframe',
        'payment_status',
        'userId',
        'repaymentDate',
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
}
