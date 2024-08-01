<?php

namespace App\Livewire\Accounts;

use App\Livewire\Forms\AnnualFeeForm;
use App\Models\AnnualFee;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AnnualFeeCapture extends Component
{
    public AnnualFeeForm $annualForm;
    public $isModalOpen = false;
    public $year;
    public $annualFees;

    public function render()
    {
        
        //get list of years from 2023 to current year
        $years = range(2023, date('Y'));

        $this->annualFees = AnnualFee::query()
                    ->where('annual_year', $this->year)
                    ->get();
        if($this->year)
            $this->sendDispatchEvent();

        return view('livewire.accounts.annual-fee-capture')->with(['years' => $years]);
    }

    public function mount()
    {
        $this->annualForm = new AnnualFeeForm($this, 'annualForm');
        // $this->sendDispatchEvent();
        if(!$this->year){
            $this->year = date('Y');
        }
            

    }

    // #[Computed]
    // public function annualFees()
    // {
    //     if(!$this->year)
    //         $this->year = date('Y');

    //     // select * from annual_fees where year = $this->year
    //     return AnnualFee::query()
    //         ->where('annual_year', $this->year)
    //         ->get();
    // }

    public function toggleModalClose()
    {
        $this->resetForm();
        $this->sendDispatchEvent();
    }

    // save annual fee
    public function saveAnnualFee()
    {
        $this->validate();

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;
            return;
        }

        $isSaved = $this->annualForm->save();

        if(!$isSaved)
        {
            $this->isModalOpen = true;
            session()->flash('error','Annual Fee details not captured');
            return;
        }

        $this->isModalOpen = false;
        session()->flash('success','Annual Fee details captured successfully');

        $this->resetForm();
        $this->sendDispatchEvent();
            
    }

    public function resetForm()
    {
        $this->annualForm->resetForm();
        $this->isModalOpen = false;
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }

}
