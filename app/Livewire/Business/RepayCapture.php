<?php

namespace App\Livewire\Business;

use NumberFormatter;
use Livewire\Component;
use App\Models\ItemCapture;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use App\Livewire\Forms\Business\RepayForm;
use App\Models\Member;
use App\Models\RepayCapture as ModelsRepayCapture;

class RepayCapture extends Component
{

    public RepayForm $repayForm;
    public $isModalOpen = false;
    public $editingRepayId = null;

    public function render()
    {

        return view('livewire.business.repay-capture')
            ->with([
                'loanBalance' => $this->getLoanBalance(),
                'session' => session(),
        ]);
    }

    public function mount()
    {
        $this->repayForm = new RepayForm($this, 'repayForm');
        $this->sendDispatchEvent();
    }

    // fetch loanBalance from the item_capture table
    
    public function getLoanBalance()
    {
        $loan = ItemCapture::where('id', $this->repayForm->item_capture_id)->first();
        
        $this->repayForm->loanBalance = $loan ? $loan->loanBalance : 0;
        return $loan ? $loan->loanBalance : 0;
    }

    #[Computed]
    public function getRepayCaptures()
    {
        return ModelsRepayCapture::query()
            ->orderBy('repaymentDate', 'desc')
            ->get();
    }

    // capture the full name of the member
    #[Computed]
    public function getMemberInfo()
    {
        $member = Member::where('coopId', $this->repayForm->coopId)->first();

        // if member is found return the member full name else return empty string
        return $member ? $member->surname.' '.$member->otherNames : 'No User';
    }


    #[Computed]
    public function getActiveItemCapturesMembers()
    {
        // get distinct values of item_capture_id from repay_captures
        return ItemCapture::select('coopId')
            ->distinct()
            ->where('payment_status', '1')
            ->orderBy('coopId', 'asc')
            ->get();
        
    }

    #[Computed]
    public function getLoanDetails()
    {
        $items = ItemCapture::query()
        ->select('coopId', 'id', 'description', 'loanBalance')
            ->where('coopId', $this->repayForm->coopId)
                ->where('payment_status', '1')
                    ->get();
        
        return $items;
    }
    

    public function toggleModalClose()
    {
        $this->editingRepayId = null;

        $this->resetForm();
        $this->sendDispatchEvent();
    }

    public function saveRepay()
    {
        $this->validate();

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;
            return;
        }

        $loanBalance = (float)$this->repayForm->loanBalance - $this->convertToPhpNumber($this->repayForm->amountToRepay);

        $repay = $this->repayForm->save($loanBalance);

        if(!$repay)
        {
            $this->isModalOpen = true;
            session()->flash('error','An error occurred while saving the payment details');
            return;
        }

        session()->flash('message','Payment saved successfully. ');

        $this->editingRepayId = null;
        $this->resetForm();
        $this->sendDispatchEvent();
    }

    #[On('delete-item-repay')]
    public function deleteOldLoanItem($id) {
        $repay = ModelsRepayCapture::find($id);

        $loanId = $repay->itemCapture->update([
            'loanBalance' => $repay->itemCapture->loanBalance + $repay->amountToRepay,
            'loanPaid' => $repay->itemCapture->loanPaid - $repay->amountToRepay
        ]);

        if($loanId)
        {
            // check if the status of the loan is 0
            if($repay->itemCapture->loanBalance == 0)
            {
                $repay->itemCapture->update([
                    'payment_status' => 0
                ]);
            }else {
                $repay->itemCapture->update([
                    'payment_status' => 1
                ]);
            }
            $repay->delete();
        }else {
            session()->flash('error','An error occurred while deleting the payment details');
            return;
        }

        session()->flash('message','Payment with id('.$id.') deleted successfully.');

        $this->sendDispatchEvent();
    }

    public function resetForm()
    {
        $this->repayForm->resetForm();
        $this->isModalOpen = false;
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }

    public function convertToPhpNumber($number)
    {
        $fmt = new NumberFormatter( 'en_US', NumberFormatter::DECIMAL );

        return $fmt->parse($number, NumberFormatter::TYPE_DOUBLE);
    }
}
