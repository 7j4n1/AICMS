<?php

namespace App\Livewire\Forms\Business;

use App\Models\ItemCapture;
use App\Models\ItemCategory;
use Carbon\Carbon;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ItemForm extends Form
{
    #[Locked]
    public $id;
    #[Validate('required|numeric|min:1|exists:members,coopId')]
    public $coopId;
    #[Validate('required|date')]
    public $buyingDate;
    #[Validate('required|numeric|min:3|max:12')]
    public $payment_timeframe;
    #[Validate('required|boolean')]
    public $payment_status=1;
    #[Validate('required|numeric|exists:item_categories,id')]
    public $category_id;

    public function save()
    {
        $this->validate();

        // Save to the database
        try {
            $item = ItemCapture::create([
                'coopId' => $this->coopId,
                'category_id' => $this->category_id,
                'quantity' => 1, // default to '1' for now
                'buyingDate' => $this->buyingDate,
                'payment_timeframe' => $this->payment_timeframe,
                'payment_status' => $this->payment_status,
                'userId' => auth('admin')->user()->id,
                'repaymentDate' => Carbon::parse($this->buyingDate)->addMonths($this->payment_timeframe),
                'loanPaid' => 0,
                'loanBalance' => ItemCategory::find($this->category_id)->price,
            ]);

            return $item;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function resetForm()
    {
        $this->coopId = '';
        $this->category_id = '';
        $this->buyingDate = date('Y-m-d');
        $this->payment_timeframe = '';
        $this->payment_status = 1;
        $this->resetErrorBag();
    }
}
