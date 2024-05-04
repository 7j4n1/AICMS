<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\ActiveLoans;
use App\Models\LoanCapture;
use App\Models\PreviousLedger2023;
use Carbon\Carbon;

class LoanDefaulters extends Component
{
    public function render()
    {
        // Get all the loan defaults from LoanCapture where the status is 1 and the repaymentDate loan date is already due
        // associate the Member model to the LoanCapture model based on guarantor1, guarantor2, guarantor3, guarantor4
        // return the view with the loans.
        // $loans = LoanCapture::query()
        //     ->where('status', 1)
        //     ->where('repaymentDate', '<', date('Y-m-d'))
        //     ->orderBy('repaymentDate', 'desc')
        //     ->get();

        // $total_loans = $loans->sum('loanAmount') ?? 0;
        
        // $total_balance = 0;

        // foreach ($loans as $loan) {
        //     $balance = ActiveLoans::where('coopId', $loan->coopId)->first()->loanBalance ?? 0;
        //     $getCurrentOutloan = PreviousLedger2023::where('coopId', $loan->coopId)->first()->loanAmount ?? 0;
        //     $total_balance += $balance;
        //     $total_balance -= $getCurrentOutloan;
        // }
        $result_query = $this->checkLoanDefaulters();

        return view('livewire.admin.reports.loan-defaulters')
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

            // check for lastPaymentDate
            // $lastPaymentDate = Carbon::parse($loan->lastPaymentDate);
            // $diff = $currentDate->diffInMonths($lastPaymentDate);
            // // check if not in the defaulters list array, then add to the defaulters list array
            // $newLoan = [
            //     'loan' => $loan,
            //     'diff' => $diff
            // ];
            // if ($diff >= 2 && !in_array($newLoan, $defaulters_list)) {
            //     $defaulters_list[] = $newLoan;
            //     $total_balance += $balance;
            //     $total_loans += $loan->loanAmount ?? 0;
            // }

        }

        // sort the defaulters list based on the diff in ascending order
        usort($defaulters_list, function ($a, $b) {
            return $a['diff'] <=> $b['diff'];
        });

        return [$defaulters_list, $total_balance, $total_loans];
        
    }
}
