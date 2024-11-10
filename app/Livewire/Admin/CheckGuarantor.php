<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use Livewire\Component;
use App\Models\ActiveLoans;
use App\Models\LoanCapture;
use App\Models\PaymentCapture;
use App\Models\PreviousLedger2023;
use Livewire\Attributes\Computed;

class CheckGuarantor extends Component
{
    public $coopId;
    private $guarantor, $guarantor_records, 
        $allguarantees, $totalLoan_guaranteed, 
        $totalOutstanding, $totalSavings, 
        $allshares, $allsavings, $full_name;
    
    public function mount()
    {
        $this->guarantor_records = $this->guarantor_records ?? [];
        $this->allguarantees = $this->allguarantees ?? [];
        $this->allshares = $this->allshares ?? 0;
        $this->allsavings = $this->allsavings ?? 0;
        $this->full_name = $this->full_name ?? '';
    }

    #[Computed]
    public function getAllIds()
    {
        return Member::query()->orderBy('coopId')->pluck('coopId')->toArray();
    }

    public function loadGuarantorData()
    {
        if(!$this->coopId) return;

        // fetch guarantor data and previous ledger data with combined savings and shares
        $this->guarantor = PaymentCapture::query()
            ->where('coopId', $this->coopId)
            ->selectRaw('COALESCE(sum(savingAmount), 0) as savings, COALESCE(sum(shareAmount), 0) as shares')
            ->first();
        
        $member = Member::where('coopId', $this->coopId)->first();
        $this->full_name = $member ? $member->surname . " " . $member->otherNames : '';

        // fetch previous ledger data
        $previousData = PreviousLedger2023::query()
            ->where('coopId', $this->coopId)
            ->selectRaw('COALESCE(sum(savingAmount), 0) as savings, COALESCE(sum(shareAmount), 0) as shares')
            ->first();

        // combine previous ledger data with guarantor data
        $this->allsavings = ($this->guarantor->savings ?? 0) + ($previousData->savings ?? 0);
        $this->allshares = ($this->guarantor->shares ?? 0) + ($previousData->shares ?? 0);
        $this->totalSavings = $this->allsavings + $this->allshares;

        // fetch all loan records for the guarantor
        $this->guarantor_records = LoanCapture::where('coopId', $this->coopId)
            ->where('status', 1)
            ->get();

        // fetch all loan records where the guarantor is listed
        $this->allguarantees = LoanCapture::where('status', 1)
            ->where(function($query) {
                $query->where('guarantor1', $this->coopId)
                    ->orWhere('guarantor2', $this->coopId)
                    ->orWhere('guarantor3', $this->coopId)
                    ->orWhere('guarantor4', $this->coopId);
            })
            ->get();

        // calculate total loan amount guaranteed by the guarantor
        $this->totalLoan_guaranteed = $this->allguarantees->sum('loanAmount') ?? 0;

        // calculate total outstanding loan amount for the guarantor
        $guaranteeCoopIds = $this->allguarantees->pluck('coopId');
        $this->totalOutstanding = ActiveLoans::whereIn('coopId', $guaranteeCoopIds)->sum('loanBalance') ?? 0;
    }

    public function updatedCoopId()
    {
        $this->loadGuarantorData();

        // dispatch event to open modal
        $this->sendDispatchEvent();
    }


    public function render()
    {
        
        // if($this->coopId > 0) {
        //     $this->sendDispatchEvent();
        // }
        $this->loadGuarantorData();

        return view('livewire.admin.check-guarantor')
            ->with(['guarantor' => $this->guarantor, 
            'guarantor_records' => $this->guarantor_records, 
            'guarantees' => $this->allguarantees, 
            'totalLoan_guaranteed' => $this->totalLoan_guaranteed, 
            'totalOutstanding' => $this->totalOutstanding, 
            'memberIds' => Member::query()->orderBy('coopId')->pluck('coopId')->toArray(),
            'totalSavings' => $this->totalSavings, 
            'allshares' => $this->allshares, 
            'allsavings' => $this->allsavings,
            'full_name' => $this->full_name
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
