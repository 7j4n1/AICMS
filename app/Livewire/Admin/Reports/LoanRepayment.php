<?php

namespace App\Livewire\Admin\Reports;

use App\Models\ActiveLoans;
use Livewire\Component;

class LoanRepayment extends Component
{
    public $beginning_date;
    public $ending_date;
    public function render()
    {
        $activeLoans = ActiveLoans::query()
            ->whereBetween('lastPaymentDate', [$this->beginning_date, $this->ending_date])->get();
        $total_loans = $activeLoans->sum('loanAmount');
        $total_balance = $activeLoans->sum('loanBalance');

        if($this->beginning_date == null)
            $this->beginning_date = date('Y-m-d', strtotime('first day of this month'));
        else
            $this->sendDispatchEvent();

        if($this->ending_date == null)
            $this->ending_date = date('Y-m-d');

        return view('livewire.admin.reports.loan-repayment')
            ->with(['activeLoans' => $activeLoans, 'total_loans' => $total_loans, 'total_balance' => $total_balance]);
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
