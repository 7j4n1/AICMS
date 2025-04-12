<?php

namespace App\Livewire\Accounts;

use App\Models\Member;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Livewire\Forms\SpecialSaveDeductionForm;
use App\Models\SpecialSaveDeduction as ModelsSpecialSaveDeduction;

class SpecialSaveDeduction extends Component
{
    use WithPagination;

    protected $paginationTheme = "bootstrap";

    protected $specialSaveDeductions;
    public SpecialSaveDeductionForm $paymentForm;
    public $isModalOpen = false;
    public $editingSpSavingId = null;

    public $prev_amount = 0;

    public $paginate = 10;
    public $search = '';


    public function render()
    {
        
        $this->specialSaveDeductions = ModelsSpecialSaveDeduction::query()
            ->orWhere('coopId', 'like', '%'.$this->search.'%')
            ->orWhere('paymentDate', 'like', '%'.$this->search.'%')
            ->orderByDesc('paymentDate')
            ->paginate($this->paginate);

        return view('livewire.accounts.special-save-deduction', [
            'records' => $this->specialSaveDeductions
        ])->with(['session' => session(), 'fullname' => $this->getMemberInfo()]);
    }

    public function mount() : void
    {
        $this->paymentForm = new SpecialSaveDeductionForm($this, 'paymentForm');
    }

    public function getMemberInfo(): string
    {
        $member = Member::where('coopId', $this->paymentForm->coopId)->first();

        // if member is found return the member full name else return empty string
        return $member ? $member->surname.' '.$member->otherNames : 'User not found';
    }

    public function resetForm(): void
    {
        $this->paymentForm->resetForm();
    }

    #[On('save-payments')]
    public function savePayment($id,$debitAmount)
    {
        $this->paymentForm->coopId = $id;
        $this->paymentForm->debitAmount = $debitAmount;


        $this->paymentForm->validate();

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;

            return;
        }


        $this->paymentForm->save();

        
        session()->flash('success','Record saved successfully');

        $this->paymentForm->resetForm();
        $this->isModalOpen = false;

        $this->sendDispatchEvent();
        
    }

    #[On('delete-payments')]
    public function deletePayment($id) {
        ModelsSpecialSaveDeduction::find($id)->delete();

        session()->flash('message','Record deleted successfully.');
        
        $this->sendDispatchEvent();
    }

    public function toggleModalOpen()
    {
        $this->isModalOpen = true;
        $this->editingSpSavingId = null;
        $this->resetForm();
        $this->sendDispatchEvent();
    }

    public function toggleModalClose()
    {
        $this->isModalOpen = false;
        $this->editingSpSavingId = null;
        $this->resetForm();
        $this->sendDispatchEvent();
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }
}
