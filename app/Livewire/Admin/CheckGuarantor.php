<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use Livewire\Component;
use App\Models\ActiveLoans;
use App\Models\LoanCapture;
use App\Models\PaymentCapture;

class CheckGuarantor extends Component
{
    public $coopId=0;
    public function render()
    {
        $memberIds = Member::orderBy("coopId","asc")->get(['coopId']);
        $guarantor = PaymentCapture::query()
            ->where('coopId', $this->coopId)
            ->selectRaw('SUM(payment_captures.savingAmount) as savings, SUM(payment_captures.shareAmount) as shares')
            ->first();

        $guarantor_records = LoanCapture::query()
            ->where('coopId', $this->coopId)
            ->where('status', 1)
            ->get();

        $guarantees = LoanCapture::query()
            ->where('status', 1)
            ->where('guarantor1', $this->coopId)
            ->orWhere('guarantor2', $this->coopId)
            ->orWhere('guarantor3', $this->coopId)
            ->orWhere('guarantor4', $this->coopId)
            ->get();

        if($guarantees->count() > 0)
            $totalLoan_guaranteed = $guarantees->sum('loanAmount') ?? 0;
        else
            $totalLoan_guaranteed = 0;

        $totalOutstanding = 0;
        foreach ($guarantees as $guarantee) {
            $outstanding = ActiveLoans::where('coopId', $guarantee->coopId)->first()->loanBalance;
            $totalOutstanding += $outstanding;
        }

        if($this->coopId > 0) {
            $this->sendDispatchEvent();
        }

        return view('livewire.admin.check-guarantor')
            ->with(['guarantor' => $guarantor, 'guarantor_records' => $guarantor_records, 
            'guarantees' => $guarantees, 'totalLoan_guaranteed' => $totalLoan_guaranteed, 
            'totalOutstanding' => $totalOutstanding, 'memberIds' => $memberIds]);
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
