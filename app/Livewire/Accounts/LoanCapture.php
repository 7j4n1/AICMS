<?php

namespace App\Livewire\Accounts;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Livewire\Forms\LoanForm;
use App\Models\LoanCapture as ModelsLoanCapture;
use App\Models\Member;

class LoanCapture extends Component
{
    use WithPagination;

    protected $paginationTheme = "bootstrap";

    protected $loans;
    public LoanForm $loanForm;
    public $isModalOpen = false;
    public $editingLoanId = null;

    private $paginate = 10;


    public function render()
    {
        $memberIds = Member::query()
            ->orderBy('coopId', 'asc')
            ->get(['coopId']);

        $this->loans = ModelsLoanCapture::query()
            ->orderBy('coopId', 'asc')
            ->get();

        return view('livewire.accounts.loan-capture',[
            'loans' => $this->loans,
            'memberIds' => $memberIds,
        ])->with(['session' => session()]);
    }

    public function mount()
    {
        $this->loanForm = new LoanForm($this, 'loanForm');
    }

    public function resetForm()
    {
        $this->loanForm = new LoanForm($this, 'loanForm');
    }

    public function saveLoan()
    {
        $this->validate();

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;
            return;
        }

        $this->loanForm->save();

        
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
        ModelsLoanCapture::find($id)->delete();

        session()->flash('message','Loan details deleted successfully.');

        $this->sendDispatchEvent();
    }

    #[On('complete-loans')]
    public function completeLoan($id) {
        $loan = ModelsLoanCapture::find($id);

        $loan->completedLoan();

        session()->flash('message','Loan marked as completed successfully.');

        $this->sendDispatchEvent();
    }

    public function toggleModalOpen()
    {
        $this->isModalOpen = true;
        $this->editingLoanId = null;
        $this->resetForm();
        $this->sendDispatchEvent();
    }

    public function toggleModalClose()
    {
        $this->isModalOpen = false;
        $this->editingLoanId = null;
        $this->sendDispatchEvent();
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }
}
