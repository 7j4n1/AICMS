<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;
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
        'otherSavingsType',
        'editDates',
        'editedBy'
    ];

    protected $casts = [
        'editDates' => 'array',
        'editedBy' => 'array'
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

    public function activeLoan()
    {
        return $this->hasOne(ActiveLoans::class, 'coopId', 'coopId');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'coopId', 'coopId');
    }

    public function loanCaptures()
    {
        return $this->hasMany(LoanCapture::class, 'coopId', 'coopId');
    }

    public function completedLoan()
    {
        $activeLoan = $this->activeLoan()->first();
        $completedLoan = new CompletedLoans();
        $completedLoan->coopId = $activeLoan->coopId;
        $completedLoan->loanAmount = $activeLoan->loanAmount;
        $completedLoan->loanPaid = $activeLoan->loanPaid;
        $completedLoan->loanBalance = $activeLoan->loanBalance;
        $completedLoan->userId = $activeLoan->userId;
        $completedLoan->loanDate = $activeLoan->loanDate;
        $completedLoan->repaymentDate = $activeLoan->repaymentDate;
        $completedLoan->lastPaymentDate = $activeLoan->lastPaymentDate;
        $completedLoan->save();

        if($completedLoan){
            $this->deactivateLoanCaptures();
            $activeLoan->delete();
        }
    }

    public function deactivateLoanCaptures()
    {
        $this->loanCaptures()->update([
            'status' => DB::raw('0'),
        ]);
    }

}
