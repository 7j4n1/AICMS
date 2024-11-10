<?php

namespace App\Livewire\Accounts;

use App\Models\Member;
use Livewire\Component;
use App\Models\ActiveLoans;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Livewire\Forms\LoanForm;
use Illuminate\Support\Facades\DB;
use App\Models\LoanCapture as ModelsLoanCapture;
use Livewire\Attributes\Computed;

class LoanCapture extends Component
{
    use WithPagination;

    protected $paginationTheme = "bootstrap";

    protected $loans;
    public LoanForm $loanForm;
    public $isModalOpen = false;
    public $editingLoanId = null;

    public $paginate = 25;
    public $search = '';


    public function render()
    {
        $this->loans = ModelsLoanCapture::query()
            ->orWhere('coopId', 'like', '%'.$this->search.'%')
            ->orderBy('coopId', 'asc')
            ->paginate($this->paginate);

        return view('livewire.accounts.loan-capture')->with(['session' => session(),
            'loans' => $this->loans]);
    }

    #[Computed(false, 3600, true)]
    public function getMembersId()
    {
        return Member::query()->orderBy('coopId')->pluck('coopId')->toArray();

    }

    public function mount()
    {
        $this->loanForm = new LoanForm($this, 'loanForm');
    }

    public function resetForm()
    {
        $this->loanForm->resetForm();
    }

    public function saveLoan()
    {
        $this->loanForm->validate();

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;
            session()->flash('error','Some error occured');
            return;
        }

        $this->loanForm->save();

        // Cache::store('file')->forget('loans');
        
        session()->flash('success','Loan details captured successfully');

        $this->loanForm->resetForm();
        $this->isModalOpen = false;
        
        $this->sendDispatchEvent();
    }

    #[On('edit-loans')]
    public function editOldLoan($id)
    {
        $loan = ModelsLoanCapture::find($id);

        if(!$loan){

            session()->flash('error','Loan Id not found.');
            $this->toggleModalClose();

            return;
        }

        $this->loanForm->fill($loan->toArray());

        $this->editingLoanId = $id;

        $this->isModalOpen = true;
        $this->sendDispatchEvent();
    }

    public function updateLoan()
    {
        // start transaction
        DB::beginTransaction();

        try{

            if(!$this->getErrorBag()->isEmpty())
            {
                $this->isModalOpen = true;
                return;
            }

            $loan = ModelsLoanCapture::find($this->editingLoanId);

            

            $this->loanForm->loanAmount = $this->loanForm->convertToPhpNumber($this->loanForm->loanAmount); 
            // get the previous loan amount
            $prev_amount = $loan->loanAmount;
            $loan->update($this->loanForm->toArray());
            // updat the repayment date
            $loan->repaymentDate = date('Y-m-d', strtotime($loan->loanDate. ' + 540 days'));
            // update the edit dates
            $loan->updateEditDates($prev_amount);
            // update the active loan amount
            $loan->updateActiveLoanAmount();
            // save the changes
            $loan->save();

            // Commit the transaction
            DB::commit();

            $this->editingLoanId = null;

            session()->flash('message','Loan details updated successfully');

            $this->loanForm->resetForm();

            $this->isModalOpen = false;

            $this->sendDispatchEvent();

        } catch(\Exception $e){
            // Rollback the transaction if anything goes wrong
            DB::rollBack();

            session()->flash('error','Error updating loan details.');
        }
    }

    #[On('delete-loans')]
    public function deleteOldLoan($id) {
        // start transaction
        DB::beginTransaction();

        try{
            $loan = ModelsLoanCapture::find($id);
            ActiveLoans::where('coopId', $loan->coopId)->first()->delete();

            $loan->delete();

            // Commit the transaction
            DB::commit();

            session()->flash('message','Loan details deleted successfully.');

            // Cache::store('file')->forget('loans');

            $this->sendDispatchEvent();
        } catch(\Exception $e){
            // Rollback the transaction if anything goes wrong
            DB::rollBack();

            session()->flash('error','Error deleting loan details.');
        }

    }

    #[On('complete-loans')]
    public function completeLoan($id) {
        $loan = ModelsLoanCapture::find($id);

        $loan->completedLoan();

        session()->flash('message','Loan marked as completed successfully.');
        // Cache::store('file')->forget('loans');

        $this->sendDispatchEvent();
    }

    public function toggleModalOpen()
    {
        $this->isModalOpen = true;
        $this->editingLoanId = null;
        $this->resetErrorBag();
        $this->resetForm();
        $this->sendDispatchEvent();
    }

    public function toggleModalClose()
    {
        $this->isModalOpen = false;
        $this->editingLoanId = null;
        $this->resetForm();
        $this->sendDispatchEvent();
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }
}
