<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use App\Models\SpecialSaveDeduction;
use Illuminate\Support\Facades\Log;

class SpecialSaveDeductionForm extends Form
{
    public $id;
    #[Validate('required|numeric|min:1|exists:members,coopId')]
    public $coopId;
    #[Validate('required|date')]
    public $paymentDate;
    #[Validate('required')]
    public $debitAmount;
    #[Validate('required|string|min:3|max:255')]
    public $otherSavingsType = '';
    public $availableBalance = 0;
    public $creditAmount = 0;

    protected $messages = [
        'coopId.required' => 'Coop ID is required.',
        'coopId.exists' => 'Coop ID does not exist.',
        'paymentDate.required' => 'Payment date is required.',
        'paymentDate.date' => 'Payment date must be a valid date.',
        'debitAmount.required' => 'Debit amount is required.',
        'debitAmount.numeric' => 'Debit amount must be a number.',
        'debitAmount.min' => 'Debit amount must be at least 50.',
        'otherSavingsType.required' => 'Savings type is required.',
        'otherSavingsType.string' => 'Savings type must be a string.',
    ];

    public function boot()
    {
        $this->withValidator(function ($validator) {
            $validator->after(function ($validator) {
                if ($this->convertToPhpNumber($this->debitAmount) < 50) {
                    $validator->errors()->add('debitAmount', 'Debit amount must be at least 50.');
                }
            });
        });
    }

    public function save() : bool
    {
        try {
            $this->validate();

            DB::beginTransaction();

            $record = SpecialSaveDeduction::create([
                'coopId' => $this->coopId,
                'paymentDate' => $this->paymentDate,
                'type' => $this->otherSavingsType,
                'debit' => $this->debitAmount,
                'credit' => 0,
            ]);

            if(!$record) {
                Log::error('Failed to save special save deduction record', [
                    'coopId' => $this->coopId,
                    'type' => $this->otherSavingsType,
                    'paymentDate' => $this->paymentDate,
                    'debit' => $this->debitAmount,
                ]);

                return false;
            }

            // commit the transaction
            DB::commit();

            return true;


        } catch (\Exception $th) {
            Log::error('Error saving special save deduction record', [
                'error' => $th->getMessage(),
                'coopId' => $this->coopId,
                'type' => $this->otherSavingsType,
                'paymentDate' => $this->paymentDate,
                'debit' => $this->debitAmount,
            ]);

            DB::rollBack();

            return false;
        }
        
    }

    public function resetForm()
    {
        $this->coopId = '';
        $this->paymentDate = null;
        $this->debitAmount = 0;
        $this->availableBalance = 0;
        $this->creditAmount = 0;
        $this->otherSavingsType = '';
        $this->resetErrorBag();
        $this->resetValidation();
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
