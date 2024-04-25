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
        $payment = ModelsPaymentCapture::where('coopId', '=', $id)
        ->orWhere('id', '=', $id)
        ->first();

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

    public function deletePayment($id) {
        ModelsPaymentCapture::find($id)->delete();

        session()->flash('message','Payment details deleted successfully.');
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
