<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepayCapture extends Model
{
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';

    protected $fillable = [
        'coopId',
        'item_capture_id',
        'amountToRepay',
        'loanBalance',
        'repaymentDate',
        'serviceCharge',
        'userId'
    ];

    public function itemCapture()
    {
        return $this->belongsTo(ItemCapture::class,'item_capture_id');
    }

    // update the loan balance of the item capture
    public function updateLoanBalance()
    {
        $itemCapture = $this->itemCapture;
        $itemCapture->loanBalance -= (float)$this->amountToRepay;
        // update the payment status to inactive(0) if the loan balance is 0
        if($itemCapture->loanBalance == 0){
            $itemCapture->payment_status = 0;
        }
        $itemCapture->loanPaid += (float)$this->amountToRepay;
        $itemCapture->save();
    }
}
