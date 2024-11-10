<?php

namespace App\Livewire\Accounts;

use App\Models\Member;
use Livewire\Component;
use App\Models\ActiveLoans;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Livewire\Forms\PaymentForm;
use App\Models\PaymentCapture as ModelsPaymentCapture;
use Illuminate\Support\Facades\DB;

class PaymentCapture extends Component
{
    use WithPagination;

    protected $paginationTheme = "bootstrap";

    protected $payments;
    public PaymentForm $paymentForm;
    public $isModalOpen = false;
    public $editingPaymentId = null;

    public $prev_amount = 0;

    public $paginate = 10;
    public $search = '';


    
    public function render()
    {
        
        $activeLoan = ActiveLoans::where('coopId', $this->paymentForm->coopId)->first();
            

        $this->payments = ModelsPaymentCapture::query()
            ->orWhere('coopId', 'like', '%'.$this->search.'%')
            ->orWhere('paymentDate', 'like', '%'.$this->search.'%')
            ->orderByDesc('paymentDate')
            ->paginate($this->paginate);

        return view('livewire.accounts.payment-capture',[
            'payments' => $this->payments
        ])->with(['session' => session(), 'activeLoan' => $activeLoan,
            'fullname' => $this->getMemberInfo()]);
    }

    public function mount() : void
    {
        $this->paymentForm = new PaymentForm($this, 'paymentForm');        
    }

    public function getMemberInfo(): string
    {
        $member = Member::where('coopId', $this->paymentForm->coopId)->first();

        // if member is found return the member full name else return empty string
        return $member ? $member->surname.' '.$member->otherNames : 'User not found';
    }

    public function resetForm(): void
    {
        $this->paymentForm->resetForm();
    }

    #[On('save-payments')]
    public function savePayment($id,$totalAmount, $loanAmount, $splitOption, $savingAmount, $shareAmount, $others, $adminCharge)
    {
        $this->paymentForm->coopId = $id;
        $this->paymentForm->totalAmount = $totalAmount;
        $this->paymentForm->loanAmount = $loanAmount;
        $this->paymentForm->splitOption = $splitOption;
        $this->paymentForm->savingAmount = $savingAmount;
        $this->paymentForm->shareAmount = $shareAmount;
        $this->paymentForm->others = $others;
        $this->paymentForm->adminCharge = $adminCharge;


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
            'loanAmount' => number_format($payment->loanAmount),
            'savingAmount' => number_format($payment->savingAmount),
            'totalAmount' => number_format($payment->totalAmount),
            'paymentDate' => $payment->paymentDate,
            'others' => number_format($payment->others),
            'shareAmount' => number_format($payment->shareAmount),
            'userId' => $payment->userId,
            'adminCharge' => $payment->adminCharge,
        ]);

        $this->editingPaymentId = $id;
        $this->prev_amount = $payment->loanAmount ?? 0;

        $this->isModalOpen = true;
        $this->sendDispatchEvent();

    }

    #[On('update-payments')]
    public function updatePayment($id,$totalAmount, $loanAmount, $splitOption, $savingAmount, $shareAmount, $others, $adminCharge, $prevAmount)
    {
        // start transaction
        DB::beginTransaction();

        try{

        
            $this->paymentForm->coopId = $id;
            $this->paymentForm->totalAmount = $this->paymentForm->convertToPhpNumber($totalAmount);
            $this->paymentForm->loanAmount = $this->paymentForm->convertToPhpNumber($loanAmount);
            $this->paymentForm->splitOption = $splitOption;
            $this->paymentForm->savingAmount = $this->paymentForm->convertToPhpNumber($savingAmount);
            $this->paymentForm->shareAmount = $this->paymentForm->convertToPhpNumber($shareAmount);
            $this->paymentForm->others = $this->paymentForm->convertToPhpNumber($others);
            $this->paymentForm->adminCharge = $adminCharge;

            $this->validate();

            if(!$this->getErrorBag()->isEmpty())
            {
                $this->isModalOpen = true;
                return;
            }

            $payment = ModelsPaymentCapture::find($this->editingPaymentId);

            $payment->update($this->paymentForm->toArray());

            // update the loan based on the previous amount and new
            $payment->updateLoan($prevAmount, $this->paymentForm->convertToPhpNumber($loanAmount));

            // Commit the transaction
            DB::commit();

            $this->editingPaymentId = null;

            session()->flash('message','Payment details updated successfully');

            $this->paymentForm->resetForm();

            $this->isModalOpen = false;

            $this->sendDispatchEvent();
        } catch(\Exception $e)
        {
            // Rollback the transaction if anything goes wrong
            DB::rollBack();

            session()->flash('error','Error updating payment.');
        }
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
