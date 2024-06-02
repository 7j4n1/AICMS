<?php

namespace App\Livewire\Business;

use Livewire\Component;
use App\Models\ItemCategory;
use Livewire\Attributes\Computed;
use App\Livewire\Forms\Business\CategoryForm;
use Livewire\Attributes\On;

class Category extends Component
{

    public CategoryForm $catForm;
    public $isModalOpen = false;
    public $editingCatId = null;

    public function render()
    {
        return view('livewire.business.category');
    }

    #[Computed]
    public function categories()
    {
        return ItemCategory::query()
            ->orderBy('id', 'desc')
            ->get();
    }

    public function mount()
    {
        $this->catForm = new CategoryForm($this, 'catForm');
        
        $this->sendDispatchEvent();
    }

    public function toggleModalClose()
    {
        $this->editingCatId = null;

        $this->resetForm();
        $this->sendDispatchEvent();
    }

    public function saveCat()
    {
        $this->validate();

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;
            return;
        }

        $category = $this->catForm->save();

        if(!$category)
        {
            $this->isModalOpen = true;
            session()->flash('error', 'Failed to save category');
            return;
        }

        unset($this->categories);

        session()->flash('success', 'Category saved successfully');
        $this->resetForm();
    
        $this->sendDispatchEvent();
        
    }

    #[On('edit-category')]
    public function editCategory($id)
    {
        $category = ItemCategory::find($id);

        if(!$category){

            session()->flash('error','Category not found.');
            $this->toggleModalClose();

            return;
        }
            
        $this->catForm->fill([
            'name' => $category->name,
            'price' => $category->price,
        ]);

        $this->editingCatId = $id;

        $this->isModalOpen = true;
        $this->sendDispatchEvent();
    }

    public function updateCat()
    {

        if(!$this->getErrorBag()->isEmpty())
        {
            $this->isModalOpen = true;
            return;
        }

        $category = ItemCategory::find($this->editingCatId);

        $category->update([
            'name' => $this->catForm->name,
            'price' => $this->catForm->price,
        ]);

        $this->editingCatId = null;

        session()->flash('message','Category details updated successfully');

        $this->resetForm();

        $this->sendDispatchEvent();
    }


    #[On('delete-category')]
    public function deleteOldCategory($id) {
        ItemCategory::find($id)->delete();

        session()->flash('message','Category deleted successfully.');

        $this->sendDispatchEvent();
    }

    public function resetForm()
    {
        $this->catForm->resetForm();
        $this->isModalOpen = false;
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }
}
