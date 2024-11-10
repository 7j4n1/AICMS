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
        'userId',
        'editDates',
        'editedBy'
    ];

    protected $casts = [
        'editDates' => 'array',
        'editedBy' => 'array'
    ];

    public function itemCapture()
    {
        return $this->belongsTo(ItemCapture::class,'item_capture_id');
    }

    // update the edit dates
    public function updateEditDates()
    {
        // get the current edit dates or initialize an empty array
        $dates = $this->editDates ?? [];

        // add the current date to the beginning of the array
        array_unshift($dates, now());

        // keep only the last 3 edit dates
        $this->editDates = array_slice($dates, 0, 3);

        // get the current edited by or initialize an empty array
        $editedBy = $this->editedBy ?? [];

        // add the current user to the beginning of the array
        array_unshift($editedBy, auth('admin')->user()->name);

        // keep only the last 3 edited by
        $this->editedBy = array_slice($editedBy, 0, 3);
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
