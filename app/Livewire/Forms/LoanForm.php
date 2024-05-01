<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\LoanCapture;
use Livewire\Attributes\Validate;

class LoanForm extends Form
{
    public $id;
    #[Validate('required|numeric|min:1|exists:members,coopId')]
    public $coopId;
    #[Validate('required|numeric')]
    public $loanAmount;
    #[Validate('required|date')]
    public $loanDate;
    #[Validate('required|numeric|min:1|exists:members,coopId')]
    public $guarantor1;
    public $guarantor2;
    public $guarantor3;
    public $guarantor4;
    #[Validate('required|boolean')]
    public $status = 1;

    protected $messages = [
        'coopId.required' => 'The Coop ID field is required.',
        'coopId.numeric' => 'The Coop ID field must be a number.',
        'coopId.min' => 'The Coop ID field must be at least 1.',
        'coopId.notIn' => 'Loan exists for the Coop ID.',
        'loanAmount.required' => 'The Loan Amount field is required.',
        'loanAmount.numeric' => 'The Loan Amount field must be a number.',
        'loanDate.required' => 'The Loan Date field is required.',
        'loanDate.date' => 'The Loan Date field must be a date.',
        'status.required' => 'The Status field is required.',
    ];

    public function save()
    {
        $this->validate();
        $loan = LoanCapture::create([
            'coopId' => $this->coopId,
            'loanAmount' => $this->loanAmount,
            'loanDate' => $this->loanDate,
            'guarantor1' => $this->guarantor1,
            'guarantor2' => $this->guarantor2,
            'guarantor3' => $this->guarantor3,
            'guarantor4' => $this->guarantor4,
            'status' => $this->status,
            'userId' => auth('admin')->user()->id,
            'repaymentDate' => date('Y-m-d', strtotime($this->loanDate. ' + 540 days'))
        ]);

        if(!$loan)
            return false;
        try {
            $loan->scopeAddToActiveLoan();
        } catch (\Exception $th) {
            return false;
        }
        

        return true;
    }

    public function resetForm()
    {
        $this->coopId = null;
        $this->loanAmount = null;
        $this->loanDate = null;
        $this->guarantor1 = null;
        $this->guarantor2 = null;
        $this->guarantor3 = null;
        $this->guarantor4 = null;
        $this->status = null;
    }
}
