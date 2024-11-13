<?php

namespace App\Livewire\Accounts;

use App\Livewire\Forms\AnnualFeeForm;
use App\Models\AnnualFee;
use Illuminate\Support\Facades\DB;
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

        if($this->annualFees->count() > 0)
        {
            $this->sendDispatchEvent();
        }
        
        return view('livewire.accounts.annual-fee-capture')->with(['years' => $years, 'session'=>session()]);
    }

    public function mount()
    {
        $this->annualForm = new AnnualFeeForm($this, 'annualForm');
        // $this->sendDispatchEvent();
        if(!$this->year){
            $this->year = date('Y');
        }
        

    }

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

        $this->resetForm();

        session()->flash('success','Annual Fee details captured successfully');
     
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

    public function deleteAnnualYear()
    {
        try {

            // start transaction
            DB::beginTransaction();
    
            $annualFees = $this->annualFees;
    
            if($annualFees->count() > 0)
            {
                // delete all annual fees for the year
                $annualFees->each(function($fee){
                    $fee->delete();
                });
            }

            DB::commit();
    
            session()->flash('success','Annual Fee for '.$this->year.' deleted successfully');
            
            $this->sendDispatchEvent();

        } catch (\Throwable $th) {
            
            // rollback transaction
            DB::rollBack();

            session()->flash('error','Annual Fee for '.$this->year.' not deleted');

        }

        
    }

}
