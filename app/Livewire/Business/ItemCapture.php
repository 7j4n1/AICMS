<?php

namespace App\Livewire\Business;

use App\Models\Member;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\ItemCategory;
use Livewire\Attributes\Computed;
use App\Livewire\Forms\Business\ItemForm;
use App\Models\ItemCapture as ModelsItemCapture;
use Illuminate\Support\Facades\DB;

class ItemCapture extends Component
{

    public ItemForm $itemForm;
    public $isModalOpen = false;
    public $editingItemId = null;

    public function render()
    {

        return view('livewire.business.item-capture')->with(['fullname' => $this->getMemberDetails()]);
    }

    #[Computed]
    public function itemcaptures()
    {
        return ModelsItemCapture::query()
            ->orderBy('buyingDate', 'desc')
            ->limit(500)
            ->get();
    }

    #[Computed]
    public function itemcategories()
    {
        return ItemCategory::query()
            ->orderBy('id', 'asc')
            ->get();
    }

    // get the details of the member through the coopId

    public function getMemberDetails()
    {
        $mem = Member::query()->where('coopId', $this->itemForm->coopId)->first();
        if($mem)
        {
            return $mem->surname.' '.$mem->otherNames;
        }
        return 'Member not found';
    }

    public function mount()
    {
        $this->itemForm = new ItemForm($this, 'itemForm');
        $this->sendDispatchEvent();

    }

    public function toggleModalClose()
    {
        $this->editingItemId = null;

        $this->resetForm();
        $this->sendDispatchEvent();
    }

    public function saveItem()
    {
        $this->validate();

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;
            return;
        }

        $item = $this->itemForm->save();

        if(!$item)
        {
            $this->isModalOpen = true;
            session()->flash('error','An error occurred while saving the entry item');
            return;
        }

        session()->flash('success','Loan item added successfully');

        $this->editingItemId = null;
        $this->resetForm();
        $this->sendDispatchEvent();
    }

    #[On('delete-item-capture')]
    public function deleteOldLoanItem($id) {
        try{
            DB::beginTransaction();

            ModelsItemCapture::find($id)->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error','An error occurred while deleting the loan item');
            $this->sendDispatchEvent();
            
            return;
        }

        session()->flash('message','Loan item capture with id('.$id.') deleted successfully.');

        $this->sendDispatchEvent();
    }

    public function resetForm()
    {
        $this->itemForm->resetForm();
        $this->isModalOpen = false;
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }
}
