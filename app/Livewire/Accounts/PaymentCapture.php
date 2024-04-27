<?php

namespace App\Livewire\Accounts;

use Livewire\Component;
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

    private $paginate = 10;

    
    public function render()
    {
        // if($this->paymentForm->splitOption >= 0 && $this->paymentForm->totalAmount > 0) {
        //     // subtract the loan amount from the total amount to get the saving amount and shares amount
        //     $remainBal = $this->paymentForm->totalAmount - $this->paymentForm->loanAmount;
        //     // calculate the percentage of the share amount and saving amount based on the splitOption which is a percentage
        //     $this->paymentForm->shareAmount = ($this->paymentForm->splitOption / 100) * $remainBal;
        //     $this->paymentForm->savingAmount = $remainBal - $this->paymentForm->shareAmount;
            
        // }
            
            $total = $this->paymentForm->totalAmount;
            if($total > 0){
                $loan = $this->paymentForm->loanAmount;
                $split = $this->paymentForm->splitOption;
                $charge = $this->paymentForm->adminCharge;
                
                $saving = $total - $loan;
                $share = $total - $loan;
                if ($split > 0) {
                    $remainBalance = $total - $loan;
                    if($charge > 0)
                        $remainBalance = $remainBalance - $charge;
                    if($this->paymentForm->others > 0)
                        $remainBalance = $remainBalance - $this->paymentForm->others;
                    $share = ($split / 100) * $remainBalance;
                    $saving = $remainBalance - $share;
                    
                }
                $this->paymentForm->savingAmount = $saving;
                $this->paymentForm->shareAmount = $share;
            }

        $this->payments = ModelsPaymentCapture::query()
            ->orderBy('paymentDate', 'asc')
            ->get();

        return view('livewire.accounts.payment-capture',[
            'payments' => $this->payments
        ])->with(['session' => session()]);
    }

    public function mount()
    {
        $this->paymentForm = new PaymentForm($this, 'paymentForm');        
    }

    public function resetForm()
    {
        $this->paymentForm = new PaymentForm($this, 'paymentForm');
    }

    public function savePayment()
    {
        $this->validate();

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

        $this->paymentForm->fill($payment->toArray());

        $this->editingPaymentId = $id;

        $this->isModalOpen = true;
        $this->sendDispatchEvent();

    }

    public function updatePayment()
    {

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;
            return;
        }

        $payment = ModelsPaymentCapture::find($this->editingPaymentId);

        $payment->update($this->paymentForm->toArray());

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
        $this->sendDispatchEvent();
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }
}
