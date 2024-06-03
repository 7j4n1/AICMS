<?php

namespace App\Livewire\Forms\Business;

use Livewire\Form;
use NumberFormatter;
use App\Models\RepayCapture;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;

class RepayForm extends Form
{
    #[Locked]
    public $id;
    #[Validate('required|numeric|min:1|exists:members,coopId')]
    public $coopId;
    #[Validate('required|uuid|exists:item_captures,id')]
    public $item_capture_id;
    #[Validate('required')]
    public $amountToRepay;
    #[Validate('required|date')]
    public $repaymentDate;
    #[Validate('required|numeric|min:0|max:99999999999.99')]
    public $serviceCharge=0;
    #[Validate('required|numeric')]
    public $loanBalance;

    public function boot()
    {
        $this->withValidator(function ($validator) {
            $validator->after(function ($validator) {
                $item = \App\Models\ItemCapture::find($this->item_capture_id);

                // check if amountToRepay is valid 
                $result = $this->convertToPhpNumber($this->amountToRepay);
                if ($result === false) {
                    $validator->errors()->add('amountToRepay', 'The amount to repay must be a valid number.');
                }else {
                    
                    if($item && $item->loanBalance < $result){
                        $validator->errors()->add('amountToRepay', 'The amount to repay must not be more than the balance.');
                    }
                    
                    if ($result < 100) {
                        $validator->errors()->add('amountToRepay', 'The amount to repay must be at least 100.');
                    }
                }
                
            });
        });
    }

    public function save($balance)
    {
        $this->validate();

        // Save to the database
        try {
            $repayment = RepayCapture::create([
                'coopId' => $this->coopId,
                'item_capture_id' => $this->item_capture_id,
                'amountToRepay' => $this->convertToPhpNumber($this->amountToRepay),
                'repaymentDate' => $this->repaymentDate,
                'loanBalance' => $balance,
                'serviceCharge' => $this->serviceCharge,
                'userId' => auth('admin')->user()->id,
            ]);

            if($repayment){
                $repayment->updateLoanBalance();
            }

            return $repayment;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function resetForm()
    {
        $this->coopId = '';
        $this->item_capture_id = '';
        $this->amountToRepay = 0;
        $this->repaymentDate = date('Y-m-d');
        $this->serviceCharge = 0.00;
    }

    /**
     * Convert a number from en-US locale to PHP number
     *
     * @param string $number
     * @return float|false
     */
    public function convertToPhpNumber($number)
    {
        $fmt = new NumberFormatter( 'en_US', NumberFormatter::DECIMAL );

        return $fmt->parse($number, NumberFormatter::TYPE_DOUBLE);
    }
}
