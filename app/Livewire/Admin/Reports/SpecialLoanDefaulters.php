<?php

namespace App\Livewire\Admin\Reports;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\LoanCapture;

class SpecialLoanDefaulters extends Component
{
    public function render()
    {
       
        $result_query = $this->checkLoanDefaulters();
        return view('livewire.admin.reports.special-loan-defaulters')
            ->with(['loans' => $result_query[0], 'total_loans' => $result_query[2], 'total_balance' => $result_query[1]]);
    }

    public function searchResult()
    {
        $this->sendDispatchEvent();
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }

    private function checkLoanDefaulters()
    {
        $currentDate = Carbon::now();
        $minConsequtiveDefault = 2; // Minimum number of months a member can default before being tagged as a defaulter



        $loans = LoanCapture::query()
            ->where('loan_type', 'special')
            ->where('status', 1) // Filter ongoing loans only
            ->with('activeLoan') // eager loading related loan balance
            ->orderBy('repaymentDate', 'asc')
            ->get();
        
        $total_balance = 0;
        $total_loans = 0;
        $defaulters_list = [];

        // get the list of defaulters where the repaymentDate is less than the current date
        // and tag members on active loan as defaulter if He/she refuse to pay two/three/four/five/..... months consecutively.
        
        foreach ($loans as $loan) {
            $repaymentDate = Carbon::parse($loan->repaymentDate);
            $diffInMonths = $currentDate->diffInMonths($repaymentDate);
            

            if ($diffInMonths >= $minConsequtiveDefault) {
                $balance = $loan->activeLoan->loanBalance ?? 0;
                $defaulters_list[] = [
                    'loan' => $loan,
                    'diff' => $diffInMonths,
                    'balance' => $balance,
                ];
                
                $total_balance += $balance;
                $total_loans += $loan->loanAmount;
            }

        }

        // sort the defaulters list based on the diff in ascending order
        usort($defaulters_list, function ($a, $b) {
            return $a['diff'] <=> $b['diff'];
        });

        return [$defaulters_list, $total_balance, $total_loans];
        
    }
}
