<?php

namespace App\Livewire\Accounts;

use Livewire\Component;
use App\Models\ActiveLoans;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Livewire\Forms\PaymentForm;
use App\Models\PaymentCapture as ModelsPaymentCapture;

class PaymentCapture extends Component
{
    use WithPagination;

    protected $paginationTheme = "bootstrap";

    protected $payments;
    public PaymentForm $paymentForm;
    public $isModalOpen = false;
    public $editingPaymentId = null;

    public $prev_amount = 0;

    public $paginate = 25;
    public $search = '';

    
    public function render()
    {
        // if($this->paymentForm->splitOption >= 0 && $this->paymentForm->totalAmount > 0) {
        //     // subtract the loan amount from the total amount to get the saving amount and shares amount
        //     $remainBal = $this->paymentForm->totalAmount - $this->paymentForm->loanAmount;
        //     // calculate the percentage of the share amount and saving amount based on the splitOption which is a percentage
        //     $this->paymentForm->shareAmount = ($this->paymentForm->splitOption / 100) * $remainBal;
        //     $this->paymentForm->savingAmount = $remainBal - $this->paymentForm->shareAmount;
            
        // }
        
            
            // $total = $this->paymentForm->totalAmount;
            // if($total > 0){
            //     $loan = $this->paymentForm->loanAmount;
            //     $split = $this->paymentForm->splitOption;
            //     $charge = $this->paymentForm->adminCharge;
                
            //     // $saving = $total - $loan;
            //     // $share = $total - $saving;
            //     if ($split > 0) {
            //         $remainBalance = $total - $loan;
            //         if($charge > 0)
            //             $remainBalance = $remainBalance - $charge;
            //         if($this->paymentForm->others > 0)
            //             $remainBalance = $remainBalance - $this->paymentForm->others;
            //         $share = ($split / 100) * $remainBalance;
            //         $saving = $remainBalance - $share;
                    
            //         $this->paymentForm->savingAmount = $saving;
            //         $this->paymentForm->shareAmount = $share;
            //     }
                
            // }
        $activeLoan = ActiveLoans::where('coopId', $this->paymentForm->coopId)->first();
            

        $this->payments = ModelsPaymentCapture::query()
            ->orWhere('coopId', 'like', '%'.$this->search.'%')
            ->orWhere('loan_type', 'like', '%'.$this->search.'%')
            ->orWhere('paymentDate', 'like', '%'.$this->search.'%')
            ->orderByDesc('paymentDate')
            ->paginate($this->paginate);

        return view('livewire.accounts.payment-capture',[
            'payments' => $this->payments
        ])->with(['session' => session(), 'activeLoan' => $activeLoan]);
    }

    public function mount()
    {
        $this->paymentForm = new PaymentForm($this, 'paymentForm');        
    }

    public function resetForm()
    {
        $this->paymentForm->resetForm();
    }

    #[On('save-payments')]
    public function savePayment($id,$totalAmount, $loanAmount, $splitOption, $savingAmount, $shareAmount, $others, $adminCharge, $hajj, $ileya, $school, $kids, $loanType)
    {
        $this->paymentForm->coopId = $id;
        $this->paymentForm->totalAmount = $totalAmount;
        $this->paymentForm->loanAmount = $loanAmount;
        $this->paymentForm->splitOption = $splitOption;
        $this->paymentForm->savingAmount = $savingAmount;
        $this->paymentForm->shareAmount = $shareAmount;
        $this->paymentForm->others = $others;
        $this->paymentForm->adminCharge = $adminCharge;
        $this->paymentForm->hajj_savings = $hajj;
        $this->paymentForm->ileya_savings = $ileya;
        $this->paymentForm->school_fees_savings = $school;
        $this->paymentForm->kids_savings = $kids;
        $this->paymentForm->loan_type = $loanType;


        $this->paymentForm->validate();

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;

            return;
        }


        $this->paymentForm->save();

        
        session()->flash('success','Payment details captured successfully');

        $this->paymentForm->resetForm();
        $this->isModalOpen = false;

        $this->sendDispatchEvent();
        
    }
    #[On('edit-payments')]
    public function editOldPayment($id)
    {
        $payment = ModelsPaymentCapture::find($id);

        if(!$payment){

            session()->flash('error','Payment Id not found.');
            $this->toggleModalClose();

            return;
        }

        $this->paymentForm->fill([
            'coopId' => $payment->coopId,
            'splitOption' => $payment->splitOption,
            'loanAmount' => number_format($payment->loanAmount, 2),
            'savingAmount' => number_format($payment->savingAmount, 2),
            'totalAmount' => number_format($payment->totalAmount,2),
            'paymentDate' => $payment->paymentDate,
            'others' => number_format($payment->others, 2),
            'shareAmount' => number_format($payment->shareAmount, 2),
            'hajj_savings' => number_format($payment->hajj_savings, 2),
            'ileya_savings' => number_format($payment->ileya_savings, 2),
            'school_fees_savings' => number_format($payment->school_fees_savings, 2),
            'kids_savings' => number_format($payment->kids_savings, 2),
            'loan_type' => $payment->loan_type,
            'userId' => $payment->userId,
            'adminCharge' => $payment->adminCharge,
        ]);

        $this->editingPaymentId = $id;
        $this->prev_amount = $payment->loanAmount ?? 0;

        $this->isModalOpen = true;
        $this->sendDispatchEvent();

    }

    #[On('update-payments')]
    public function updatePayment($id,$totalAmount, $loanAmount, $splitOption, $savingAmount, $shareAmount, $others, $adminCharge, $prevAmount, $hajj, $ileya, $school, $kids, $loanType)
    {
        $this->paymentForm->coopId = $id;
        $this->paymentForm->totalAmount = $this->paymentForm->convertToPhpNumber($totalAmount);
        $this->paymentForm->loanAmount = $this->paymentForm->convertToPhpNumber($loanAmount);
        $this->paymentForm->splitOption = $splitOption;
        $this->paymentForm->savingAmount = $this->paymentForm->convertToPhpNumber($savingAmount);
        $this->paymentForm->shareAmount = $this->paymentForm->convertToPhpNumber($shareAmount);
        $this->paymentForm->others = $this->paymentForm->convertToPhpNumber($others);
        $this->paymentForm->adminCharge = $adminCharge;
        $this->paymentForm->hajj_savings = $this->paymentForm->convertToPhpNumber($hajj);
        $this->paymentForm->ileya_savings = $this->paymentForm->convertToPhpNumber($ileya);
        $this->paymentForm->school_fees_savings = $this->paymentForm->convertToPhpNumber($school);
        $this->paymentForm->kids_savings = $this->paymentForm->convertToPhpNumber($kids);
        $this->paymentForm->loan_type = $loanType;

        $this->validate();

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;
            return;
        }

        $payment = ModelsPaymentCapture::find($this->editingPaymentId);

        $payment->update($this->paymentForm->toArray());

        $payment->updateLoan($prevAmount);

        $this->editingPaymentId = null;

        session()->flash('message','Payment details updated successfully');

        $this->paymentForm->resetForm();

        $this->isModalOpen = false;

        $this->sendDispatchEvent();
    }

    #[On('delete-payments')]
    public function deletePayment($id) {
        ModelsPaymentCapture::find($id)->delete();

        session()->flash('message','Payment details deleted successfully.');
        
        $this->sendDispatchEvent();
    }

    public function toggleModalOpen()
    {
        $this->isModalOpen = true;
        $this->editingPaymentId = null;
        $this->resetForm();
        $this->sendDispatchEvent();
    }

    public function toggleModalClose()
    {
        $this->isModalOpen = false;
        $this->editingPaymentId = null;
        $this->resetForm();
        $this->sendDispatchEvent();
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }
}
