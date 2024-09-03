<?php

namespace App\Livewire\Accounts;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Livewire\Forms\LoanForm;
use App\Models\ActiveLoans;
use App\Models\LoanCapture as ModelsLoanCapture;
use App\Models\Member;
use Illuminate\Support\Facades\Cache;

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
    public $loanType = 'Normal';


    public function render()
    {
        $this->loans = ModelsLoanCapture::query()
            ->orWhere('coopId', 'like', '%'.$this->search.'%')
            ->orWhere('loan_type', $this->loanType)
            ->orWhere('loan_type', 'like', '%'.$this->search.'%')
            ->orWhere('loanDate', 'like', '%'.$this->search.'%')
            ->orderByDesc('loanDate')
            ->paginate($this->paginate);

        $memberIds = Cache::store('file')->remember('memberIds', now()->addMinutes(5), function () {
            return Member::query()
            ->orderBy('coopId', 'asc')
            ->get(['coopId']);
        });

        return view('livewire.accounts.loan-capture')->with(['session' => session(),'loans' => $this->loans, 'memberIds' => $memberIds]);
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

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;
            return;
        }

        $loan = ModelsLoanCapture::find($this->editingLoanId);

        $loan->update($this->loanForm->toArray());

        $this->editingLoanId = null;

        session()->flash('message','Loan details updated successfully');

        $this->loanForm->resetForm();

        $this->isModalOpen = false;

        $this->sendDispatchEvent();
    }

    #[On('delete-loans')]
    public function deleteOldLoan($id) {
        $loan = ModelsLoanCapture::find($id);
        ActiveLoans::where('coopId', $loan->coopId)->first()->delete();

        $loan->delete();

        session()->flash('message','Loan details deleted successfully.');

        // Cache::store('file')->forget('loans');

        $this->sendDispatchEvent();
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
