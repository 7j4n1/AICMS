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

    protected $fillable = [
        'coopId',
        'splitOption',
        'loanAmount',
        'savingAmount',
        'totalAmount',
        'paymentDate',
        'loan_type',
        'others',
        'shareAmount',
        'userId',
        'adminCharge',
        'hajj_savings',
        'special_savings',
        'ileya_savings',
        'school_fees_savings',
        'kids_savings'
    ];

    public function updateLoan($prev_amount)
    {
        $loan = ActiveLoans::where('coopId', $this->coopId)->first();
        if($loan)
        {
            $prevBalance = $loan->loanPaid - (float)$prev_amount;
            $newLoanPaid = $prevBalance + (float)$this->loanAmount;

            $loan->loanPaid = $newLoanPaid;
            $loan->remainingBalance = $loan->loanAmount - $newLoanPaid;
            $loan->save();
        }
        
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'coopId', 'coopId');
    }

}
