<?php

namespace App\Livewire\Forms;

use App\Models\ActiveLoans;
use App\Models\PaymentCapture;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PaymentForm extends Form
{
    public $id;
    #[Validate('required|numeric|min:1|exists:members,coopId')]
    public $coopId;
    #[Validate('required|numeric')]
    public $splitOption;
    #[Validate('required|numeric')]
    public $loanAmount = 0;
    #[Validate('required|numeric')]
    public $savingAmount = 0;
    #[Validate('required|numeric')]
    public $totalAmount = 0;
    #[Validate('required|date')]
    public $paymentDate;
    #[Validate('required|numeric')]
    public $others = 0;
    #[Validate('required|numeric')]
    public $shareAmount = 0;


    protected $messages = [
        'coopId.required' => 'The Coop ID field is required.',
        'coopId.numeric' => 'The Coop ID field must be a number.',
        'coopId.exists' => 'The Coop ID field must exists.',
        'splitOption.required' => 'The Split Option field is required.',
        'splitOption.numeric' => 'The Split Option field must be a number.',
        'loanAmount.required' => 'The Loan Amount field is required.',
        'loanAmount.numeric' => 'The Loan Amount field must be a number.',
        'savingAmount.required' => 'The Saving Amount field is required.',
        'savingAmount.numeric' => 'The Saving Amount field must be a number.',
        'totalAmount.required' => 'The Total Amount field is required.',
        'totalAmount.numeric' => 'The Total Amount field must be a number.',
        'paymentDate.required' => 'The Payment Date field is required.',
        'paymentDate.date' => 'The Payment Date field must be a date.',
        'others.required' => 'The Others field is required.',
        'others.numeric' => 'The Others field must be a number.',
        'shareAmount.required' => 'The Share Amount field is required.',
        'shareAmount.numeric' => 'The Share Amount field must be a number.',
    ];


    public function save()
    {
        $this->validate();
        $payment = PaymentCapture::create([
            'coopId' => $this->coopId,
            'splitOption' => $this->splitOption,
            'loanAmount' => $this->loanAmount,
            'savingAmount' => $this->savingAmount,
            'totalAmount' => $this->totalAmount,
            'paymentDate' => $this->paymentDate,
            'others' => $this->others,
            'shareAmount' => $this->shareAmount,
            'userId' => auth('admin')->user()->name,
        ]);

        if(!$payment)
            return false;

        if(($this->loanAmount >= 1.0) || !is_null($this->loanAmount)){
            $activeLoan = ActiveLoans::where('coopId', $this->coopId)->first();
            if($activeLoan)
            {
                $activeLoan->setPayment($this->loanAmount);
            }
        }

        return true;
    }

    public function resetForm()
    {
        $this->coopId = null;
        $this->splitOption = null;
        $this->loanAmount = null;
        $this->savingAmount = null;
        $this->totalAmount = null;
        $this->paymentDate = null;
        $this->others = null;
        $this->shareAmount = null;
    }

}
