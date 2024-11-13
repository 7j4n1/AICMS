<?php

namespace App\Livewire\Forms\Business;

use Livewire\Form;
use App\Models\ItemCategory;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;

class CategoryForm extends Form
{
    #[Locked]
    public $id;
    #[Validate('required|string|max:255|unique:item_categories,name')]
    public $name;


    public function save()
    {
        $this->validate();

        // Save to the database
        try {
            $category = ItemCategory::create([
                'name' => $this->name,
                'userId' => auth('admin')->user()->name,
            ]);

            return $category;
        } catch (\Exception $e) {
            // $this->addError('name', 'An error occurred while saving the category.');
            return null;
        }
        
    }

    public function resetForm()
    {
        $this->name = '';
    }
}
