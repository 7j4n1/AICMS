<?php

namespace App\Livewire\Business;

use App\Models\Member;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\ItemCategory;
use Livewire\Attributes\Computed;
use App\Livewire\Forms\Business\ItemForm;
use App\Models\ItemCapture as ModelsItemCapture;

class ItemCapture extends Component
{

    public ItemForm $itemForm;
    public $isModalOpen = false;
    public $editingItemId = null;
    private $fullname;
    public $categoryPrice;

    public function render()
    {

        $this->categoryPrice = $this->getCategoryPrice($this->itemForm->category_id);

        return view('livewire.business.item-capture')->with(['fullname' => $this->getMemberDetails()]);
    }

    #[Computed]
    public function itemcaptures()
    {
        return ModelsItemCapture::query()
            ->orderBy('id', 'desc')
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

    // get category price through category id selected
    public function getCategoryPrice($id)
    {
        $cat = ItemCategory::query()->where('id', $id)->first();
        if($cat)
        {
            return $cat->price;
        }
        return 0;
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
        ModelsItemCapture::find($id)->delete();

        session()->flash('message','Loan item with id('.$id.') deleted successfully.');

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
