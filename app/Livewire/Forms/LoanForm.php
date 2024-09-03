<?php

namespace App\Livewire\Forms;

use App\Models\ActiveLoans;
use Livewire\Form;
use App\Models\LoanCapture;
use Livewire\Attributes\Validate;

class LoanForm extends Form
{
    public $id;
    #[Validate('required|numeric|min:1|exists:members,coopId')]
    public $coopId;
    #[Validate('required')]
    public $loanAmount;
    #[Validate('required')]
    public $loan_type;
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

    public function boot()
    {
        $this->withValidator(function ($validator){
            $validator->after(function ($validator){
                $coopId = $this->coopId;
                $loan = ActiveLoans::where('coopId', $coopId)->where('loan_type', $this->loan_type)->first();
                if($loan)
                    $validator->errors()->add('coopId', 'Loan exists for the Coop ID.');

                if($this->guarantor1 == $this->guarantor2 || $this->guarantor1 == $this->guarantor3 || $this->guarantor1 == $this->guarantor4)
                    $validator->errors()->add('guarantor1', 'Guarantor 1 cannot be the same as Guarantor 2, 3 or 4');
                if(!is_null($this->guarantor2) && ($this->guarantor2 == $this->guarantor3 || $this->guarantor2 == $this->guarantor4))
                    $validator->errors()->add('guarantor2', 'Guarantor 2 cannot be the same as Guarantor 3 or 4');
                if(!is_null($this->guarantor3) && ($this->guarantor3 == $this->guarantor4))
                    $validator->errors()->add('guarantor3', 'Guarantor 3 cannot be the same as Guarantor 4');
            });
        });
    }

    public function save()
    {
        $this->validate();
        $duration = ($this->loan_type == "normal") ? 0 : 1;
        $durationPeriod = ($duration == 0) ? date('Y-m-d', strtotime($this->loanDate. ' + 540 days')) : date('Y-m-d', strtotime($this->loanDate. ' + 90 days'));
        $loan = LoanCapture::create([
            'coopId' => $this->coopId,
            'loanAmount' => $this->convertToPhpNumber($this->loanAmount),
            'loanDate' => $this->loanDate,
            'loan_type' => $this->loan_type,
            'guarantor1' => $this->guarantor1,
            'guarantor2' => $this->guarantor2,
            'guarantor3' => $this->guarantor3,
            'guarantor4' => $this->guarantor4,
            'status' => $this->status,
            'userId' => auth('admin')->user()->id,
            'repaymentDate' => $durationPeriod
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
        $this->loanAmount = 0;
        $this->loanDate = null;
        $this->guarantor1 = '';
        $this->guarantor2 = '';
        $this->guarantor3 = '';
        $this->guarantor4 = '';
        $this->status = 1;
        $this->loan_type = "normal";
    }

    /**
     * Convert a number from en-US locale to PHP number
     *
     * @param string $number
     * @return float
     */
    public function convertToPhpNumber($number)
    {
        return (float)str_replace(',', '', $number);
    }
}
