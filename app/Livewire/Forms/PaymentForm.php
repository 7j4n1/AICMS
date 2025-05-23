<?php

namespace App\Livewire\Forms;

use Exception;
use Livewire\Form;
use App\Models\AnnualFee;
use App\Models\ActiveLoans;
use App\Models\LoanCapture;
use App\Models\PaymentCapture;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SpecialSaveDeduction;

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
        
        try{

            $this->validate();
            // Start a database transaction
            DB::beginTransaction();
        

            $payment = PaymentCapture::create([
                'coopId' => $this->coopId,
                'splitOption' => $this->splitOption,
                'loanAmount' => $this->convertToPhpNumber($this->loanAmount),
                'savingAmount' => $this->convertToPhpNumber($this->savingAmount),
                'totalAmount' => $this->convertToPhpNumber($this->totalAmount),
                'paymentDate' => $this->paymentDate,
                'others' => $this->convertToPhpNumber($this->others),
                'shareAmount' => $this->convertToPhpNumber($this->shareAmount),
                'userId' => auth('admin')->user()->name,
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

            // Deduct pending annual fees from available shareAmount for the member across all years
            $this->deductPendingAnnualFees($this->coopId, $this->convertToPhpNumber($this->shareAmount));

            if($this->convertToPhpNumber($this->others) > 0)
            {
                SpecialSaveDeduction::create([
                    'coopId' => $this->coopId,
                    'paymentDate' => $this->paymentDate,
                    'debit' => 0,
                    'type' => $this->otherSavingsType,
                    'credit' => $this->convertToPhpNumber($this->others),
                ]);
            }

            // commit the transaction
            DB::commit();

            return true;
        } catch (\Exception $th) {
            // Log the error message
            Log::error('Error saving payment: ' . $th->getMessage(), [
                'coopId' => $this->coopId,
                'splitOption' => $this->splitOption,
                'loanAmount' => $this->convertToPhpNumber($this->loanAmount),
                'savingAmount' => $this->convertToPhpNumber($this->savingAmount),
                'totalAmount' => $this->convertToPhpNumber($this->totalAmount),
                'paymentDate' => $this->paymentDate,
                'others' => $this->convertToPhpNumber($this->others),
                'shareAmount' => $this->convertToPhpNumber($this->shareAmount),
                'userId' => auth('admin')->user()->name,
                'adminCharge' => $this->adminCharge,
            ]);
            // rollback the transaction
            DB::rollBack();

            return false;
        }
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

    /**
     * Deduct pending annual fees from available savingAmount for the member across all years.
     *
     * @param int $coopId
     * @param float $savingAmount
     * @return void
     */
    public function deductPendingAnnualFees($coopId, $savingAmount)
    {
        // Start a database transaction
        DB::beginTransaction();
        try {
            // Retrieve pending annual fees for the member in chunks to optimize memory usage
            AnnualFee::where('coopId', $coopId)
                ->where('annual_fee', '<', 0) // Pending annual fees
                ->orderBy('annual_year', 'asc')
                ->select(['id', 'annual_fee', 'annual_savings', 'total_savings'])
                ->chunkById(20, function ($pendingFees) use (&$savingAmount) {
                    // Collect batch updates for efficiency
                    $updates = [];

                    // Deduct pending annual fees from the share amount
                    foreach ($pendingFees as $annualFee) {
                        if ($savingAmount <= 0) {
                            break; // No more share amount to deduct
                        }

                        // Calculate deduction amount
                        $amount_due = $this->convertToPhpNumber($annualFee->annual_fee);
                        $deductible_amount = min($amount_due, $savingAmount);

                        // Prepare update data for batch update
                        $updates[] = [
                            'id' => $annualFee->id,
                            'annual_savings' => $deductible_amount,
                            'total_savings' => $annualFee->total_savings - $deductible_amount,
                        ];

                        // Update share amount by the deducted amount
                        $savingAmount -= $deductible_amount;

                    }

                    // Batch update pending annual fees using the single query
                    if (!empty($updates)) {
                        $query = AnnualFee::query();
                        $query->getConnection()->transaction(function () use ($query, $updates) {
                            $query->upsert($updates, ['id'], ['annual_savings', 'total_savings']);
                        });
                    }

            });

            // Commit the transaction
            DB::commit();

        } catch (\Exception $th) {
            // Log the error message
            Log::error('Error deducting pending annual fees: ' . $th->getMessage(), [
                'coopId' => $coopId,
                'savingAmount' => $savingAmount,
            ]);
            // Rollback the transaction on error
            DB::rollBack();

            // Re-throw the exception
            throw $th;
        }
    }

}
