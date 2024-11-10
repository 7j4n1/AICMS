<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'others',
        'shareAmount',
        'userId',
        'adminCharge',
        'otherSavingsType'
    ];

    public function updateLoan($prev_amount, $newAmount)
    {
        $loan = ActiveLoans::where('coopId', $this->coopId)->first();
        if($loan)
        {
            // calculate the balance after reverting the original payment
            $revertedBalance = (float)$loan->loanBalance + (float)$prev_amount;
            
            // prevent overpayment
            if($newAmount > $revertedBalance)
            {
                throw new Exception("The repayment amount exceeds the remaining loan balance.");
            }

            // revert the original payment effect
            $loan->loanPaid -= (float)$prev_amount;
            $loan->loanBalance += (float)$prev_amount;

            // Apply the new payment
            $loan->loanPaid += (float)$newAmount;
            $loan->loanBalance = $loan->loanAmount - $loan->loanPaid;

            // check if the loan is paid off or not
            // if($loan->loanBalance <= 0)
            //     $this->checkActiveLoanBalanceStatus($this->coopId);

            // Save the loan updates
            $loan->save();
        }
        
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'coopId', 'coopId');
    }

}
