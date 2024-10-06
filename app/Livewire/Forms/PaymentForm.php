<?php

namespace App\Livewire\Forms;

use Exception;
use Livewire\Form;
use App\Models\ActiveLoans;
use App\Models\LoanCapture;
use App\Models\PaymentCapture;
use Livewire\Attributes\Validate;

class PaymentForm extends Form
{
   
    public $id;
    #[Validate('required|numeric|min:1|exists:members,coopId')]
    public $coopId;
    #[Validate('required|numeric|min:0')]
    public $splitOption = 0;
    // #[Validate('required|numeric|min:0')]
    #[Validate('required')]
    public $loanAmount = 0;
    // #[Validate('required|numeric|min:0')]
    #[Validate('required')]
    public $savingAmount = 0;
    // #[Validate('required|numeric|min:0')]
    #[Validate('required')]
    public $totalAmount = 0;
    #[Validate('required|date')]
    public $paymentDate;
    // #[Validate('required|numeric|min:0')]
    #[Validate('required')]
    public $others = 0;
    // #[Validate('required|numeric|min:0')]
    #[Validate('required')]
    public $shareAmount = 0;
    #[Validate('numeric|min:0')]
    public $adminCharge = 0;
    public $otherSavingsType = '';

    // rules

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

    public function boot()
    {
        $this->withValidator(function ($validator){
            $validator->after(function ($validator){

                // check if the coopId has an active loan
                $activeLoan = ActiveLoans::where('coopId', $this->coopId)->first();
                if($activeLoan)
                {
                    if($this->convertToPhpNumber($this->loanAmount) > $activeLoan->loanBalance)
                    {
                        $validator->errors()->add('loanAmount', 'The loan amount must not be greater than the remaining amount of the active loan.');
                    }
                }else {
                    // if no active loan, and loanAmount is greater than 0, add error
                    if($this->convertToPhpNumber($this->loanAmount) > 0)
                    {
                        $this->loanAmount = 0;
                        $validator->errors()->add('loanAmount', 'The loan amount must be 0 if there is no active loan.');
                    }
                }

                if($this->convertToPhpNumber($this->totalAmount) < 100){
                    $validator->errors()->add('totalAmount', 'The total amount must be greater than 100.');
                }

                // all the sum of the amount fields must not be greater than the total amount
                $savingAmount = $this->convertToPhpNumber($this->savingAmount);
                $loanAmount = $this->convertToPhpNumber($this->loanAmount);
                $others = $this->convertToPhpNumber($this->others);
                $shareAmount = $this->convertToPhpNumber($this->shareAmount);
                $adminCharge = $this->convertToPhpNumber($this->adminCharge);

                if(($loanAmount + $savingAmount + $others + $shareAmount + $adminCharge) > $this->convertToPhpNumber($this->totalAmount)){
                    $validator->errors()->add('totalAmount', 'The sum of the amount fields must not be greater than the total amount.');
                }
    //             if(($this->loanAmount + $this->savingAmount + $this->others + $this->shareAmount + $this->adminCharge) != $this->totalAmount){
    //                 $validator->errors()->add('totalAmount', 'Your computation cannot be greater than the TOTAL.');
    //             }

            });
        });
    }


    public function save()
    {
        $this->validate();
        $payment = PaymentCapture::create([
            'coopId' => $this->coopId,
            'splitOption' => $this->splitOption,
            'loanAmount' => $this->convertToPhpNumber($this->loanAmount),
            'savingAmount' => $this->convertToPhpNumber($this->savingAmount),
            'totalAmount' => $this->convertToPhpNumber($this->totalAmount),
            'paymentDate' => $this->paymentDate,
            'others' => $this->convertToPhpNumber($this->others),
            'shareAmount' => $this->convertToPhpNumber($this->shareAmount),
            'userId' => auth('admin')->user()->id,
            'adminCharge' => $this->adminCharge,
            'otherSavingsType' => $this->otherSavingsType,
        ]);

        if(!$payment){
            // throw new Exception("Failed to create a payment.");
            return false;
        }
            
        // throw new Exception("Create a payment successfully.");
        if(($this->convertToPhpNumber($this->loanAmount) >= 1) && !is_null($this->convertToPhpNumber($this->loanAmount))){
            $activeLoan = ActiveLoans::where('coopId', $this->coopId)->first();
            if($activeLoan)
            {
                $activeLoan->setPayment($this->convertToPhpNumber($this->loanAmount));

                // check if the loan is paid off or not
                if($activeLoan->loanBalance == 0 || ($activeLoan->loanPaid == $activeLoan->loanAmount) )
                    $this->checkActiveLoanBalanceStatus($this->coopId);
                
            }
        }

        return true;
    }

    public function resetForm()
    {
        $this->coopId = null;
        $this->splitOption = 0;
        $this->loanAmount = 0;
        $this->savingAmount = 0;
        $this->totalAmount = 0;
        $this->paymentDate = null;
        $this->others = 0;
        $this->shareAmount = 0;
        $this->adminCharge = 0;
        $this->otherSavingsType = '';
        $this->resetErrorBag();
    }

    // function to convert js locale en-US to php number
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

    public function checkActiveLoanBalanceStatus($coopId)
    {
        $loan = LoanCapture::where('coopId', $coopId)->first();

        if($loan){
            $loan->completedLoan();
        }
        
    }

}
