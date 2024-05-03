<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\ActiveLoans;
use App\Models\LoanCapture;
use App\Models\PreviousLedger2023;

class LoanDefaulters extends Component
{
    public function render()
    {
        // Get all the loan defaults from LoanCapture where the status is 1 and the repaymentDate loan date is already due
        // associate the Member model to the LoanCapture model based on guarantor1, guarantor2, guarantor3, guarantor4
        // return the view with the loans.
        $loans = LoanCapture::query()
            ->where('status', 1)
            ->where('repaymentDate', '<', date('Y-m-d'))
            ->orderBy('id', 'asc')
            ->get();

        $total_loans = $loans->sum('loanAmount') ?? 0;
        
        $total_balance = 0;

        foreach ($loans as $loan) {
            $balance = ActiveLoans::where('coopId', $loan->coopId)->first()->loanBalance ?? 0;
            $getCurrentOutloan = PreviousLedger2023::where('coopId', $loan->coopId)->first()->loanAmount ?? 0;
            $total_balance += $balance;
            $total_balance -= $getCurrentOutloan;
        }

        return view('livewire.admin.reports.loan-defaulters')
            ->with(['loans' => $loans, 'total_loans' => $total_loans, 'total_balance' => $total_balance]);
    }

    public function searchResult()
    {
        $this->sendDispatchEvent();
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }
}
