<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use Livewire\Component;
use App\Models\ActiveLoans;
use App\Models\LoanCapture;
use App\Models\PaymentCapture;
use App\Models\PreviousLedger2023;

class CheckGuarantor extends Component
{
    public $coopId;
    public function render()
    {
        $memberIds = Member::orderBy("coopId","asc")->get(['coopId']);
        $guarantor = PaymentCapture::query()
            ->where('coopId','=', $this->coopId)
            ->selectRaw('sum(savingAmount) as savings, sum(shareAmount) as shares')
            ->first();

        $previousData = PreviousLedger2023::query()
            ->where('coopId', '=',$this->coopId)
            ->selectRaw('sum(savingAmount) as savings, sum(shareAmount) as shares')
            ->first();
        $preallsavings = $previousData->savings ?? 0;
        $preallshares = $previousData->shares ?? 0;
        $pretotalSavings = $preallsavings + $preallshares;

        $allsavings = $guarantor->savings ?? 0;
        $allshares = $guarantor->shares ?? 0;
        $totalSavings = $allsavings + $allshares;

        $allsavings += $preallsavings;
        $allshares += $preallshares;
        $totalSavings += $pretotalSavings;

        $guarantor_records = LoanCapture::query()
            ->where('coopId','=', $this->coopId)
            ->where('status', 1)
            ->get();

        $guarantees = LoanCapture::query()
            ->where('status', 1)
            ->where('guarantor1', $this->coopId)
            ->orWhere('guarantor2', $this->coopId)
            ->orWhere('guarantor3', $this->coopId)
            ->orWhere('guarantor4', $this->coopId)
            ->get();

        $totalLoan_guaranteed = $guarantees->sum('loanAmount') ?? 0;

        $totalOutstanding = 0;
        foreach ($guarantees as $guarantee) {
            $outstanding = ActiveLoans::where('coopId','=', $guarantee->coopId)->first()->loanBalance ?? 0;
            $totalOutstanding += $outstanding;
        }

        if($this->coopId > 0) {
            $this->sendDispatchEvent();
        }

        return view('livewire.admin.check-guarantor')
            ->with(['guarantor' => $guarantor, 'guarantor_records' => $guarantor_records, 
            'guarantees' => $guarantees, 'totalLoan_guaranteed' => $totalLoan_guaranteed, 
            'totalOutstanding' => $totalOutstanding, 'memberIds' => $memberIds,
            'totalSavings' => $totalSavings, 'allshares' => $allshares, 'allsavings' => $allsavings
        ]);
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
