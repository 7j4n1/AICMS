<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanCapture extends Model
{
    use HasFactory;
    use HasUuids;

    protected $keyType = 'string';

    protected $fillable = [
        'coopId',
        'loanAmount',
        'loanDate',
        'guarantor1',
        'guarantor2',
        'guarantor3',
        'guarantor4',
        'status',
        'userId',
        'repaymentDate',
        'editDates',
        'editAmounts',
        'editedBy'
    ];

    protected $casts = [
        'editDates' => 'array',
        'editAmounts' => 'array',
        'editedBy' => 'array'
    ];

    public function updateEditDates()
    {
        // get the current edit dates or initialize an empty array
        $dates = $this->editDates ?? [];

        // add the current date to the beginning of the array
        array_unshift($dates, now());

        // keep only the last 3 edit dates
        $this->editDates = array_slice($dates, 0, 3);

        // get the current edit amounts or initialize an empty array
        $amounts = $this->editAmounts ?? [];

        // add the current loan amount to the beginning of the array
        array_unshift($amounts, $this->loanAmount);

        // keep only the last 3 edit amounts
        $this->editAmounts = array_slice($amounts, 0, 3);

        // get the current edited by or initialize an empty array
        $editedBy = $this->editedBy ?? [];
        // add the current user to the beginning of the array
        array_unshift($editedBy, auth('admin')->user()->name);

        // keep only the last 3 edited by
        $this->editedBy = array_slice($editedBy, 0, 3);

    }


    // Method to check if loan can be approved based on savings
    public function canApplyForLoan($requestedAmount) : bool
    {
        // Calculate total savings amount
        $totalSavings = PaymentCapture::where('coopId', $this->coopId)
                        ->sum('savingAmount');
        
        // Check if requested loan amount exceeds savings
        return $requestedAmount <= $totalSavings;
    }

    public function scopeAddToActiveLoan()
    {
        // Wrap in a database transaction
        DB::transaction(function() {
            // Check if loan can be approved based on savings
            // if (!$this->canApplyForLoan($this->loanAmount)) {
            //     throw new Exception("Loan amount exceeds available savings.");
            // }

            $activeLoan = ActiveLoans::create([
                'coopId' => $this->coopId,
                'loanAmount' => $this->loanAmount,
                'loanPaid' => 0,
                'loanBalance' => $this->loanAmount,
                'userId' => $this->userId,
                'loanDate' => $this->loanDate,
                'repaymentDate' => $this->repaymentDate,
                'lastPaymentDate' => date('Y-m-d')
            ]);

            if(!$activeLoan)
                throw new Exception("Failed to create an active loan.");
                
        });
        
    }

    public function scopeAddToActiveLoanWithDate()
    {
        // Wrap in a database transaction
        DB::transaction(function() {
            // Check if loan can be approved based on savings
            // if (!$this->canApplyForLoan($this->loanAmount)) {
            //     throw new Exception("Loan amount exceeds available savings.");
            // }
            $activeLoan = ActiveLoans::create([
                'coopId' => $this->coopId,
                'loanAmount' => $this->loanAmount,
                'loanPaid' => 0,
                'loanBalance' => $this->loanAmount,
                'userId' => $this->userId,
                'loanDate' => $this->loanDate,
                'repaymentDate' => $this->repaymentDate,
                'lastPaymentDate' => $this->loanDate
            ]);

            if(!$activeLoan)
                throw new Exception("Failed to create an active loan.");
                
        });
        
    }

    public function user()
    {
        return \App\Models\Member::where('coopId', $this->coopId)->first();
    }

    public function activeLoan()
    {
        return $this->hasOne(ActiveLoans::class, 'coopId', 'coopId');
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
            static::query()->where('coopId', $this->coopId)->update([
                'status' => DB::raw('0'),
            ]);
            $activeLoan->delete();
        }
    }

    // build relationships between guarantor1, guarantor2, guarantor3, guarantor4 and members
    public function guarantor1()
    {
        return $this->belongsTo(Member::class, 'guarantor1', 'coopId');
    }
    public function guarantor2()
    {
        return $this->belongsTo(Member::class, 'guarantor2', 'coopId');
    }
    public function guarantor3()
    {
        return $this->belongsTo(Member::class, 'guarantor3', 'coopId');
    }
    public function guarantor4()
    {
        return $this->belongsTo(Member::class, 'guarantor4', 'coopId');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'coopId', 'coopId');
    }
}
