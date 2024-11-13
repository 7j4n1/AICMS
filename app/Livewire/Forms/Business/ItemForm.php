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
    #[Validate('required|string')]
    public $description;
    #[Validate('required')]
    public $price;
    #[Validate('required|date')]
    public $buyingDate;
    #[Validate('required|numeric|min:1|max:24')]
    public $payment_timeframe;
    #[Validate('required|boolean')]
    public $payment_status=1;
    #[Validate('required|numeric|exists:item_categories,id')]
    public $category_id;

    // boot method
    public function boot()
    {
        $this->withValidator(function ($validator){
            $validator->after(function ($validator){
                if($this->convertToPhpNumber($this->price) < 100){
                    $validator->errors()->add('price', 'The price amount must be greater than 100.');
                }
            });
        });
    }

    public function save()
    {
        $this->validate();

        // Save to the database
        try {
            // check if the payment timeframe is 1 - immediate payment
            $repaymentDate = ($this->payment_timeframe == 1) ? Carbon::parse($this->buyingDate)->addDay() : Carbon::parse($this->buyingDate)->addMonths($this->payment_timeframe);
            

            $item = ItemCapture::create([
                'coopId' => $this->coopId,
                'category_id' => $this->category_id,
                'price' => $this->convertToPhpNumber($this->price),
                'description' => $this->description,
                'quantity' => 1, // default to '1' for now
                'buyingDate' => $this->buyingDate,
                'payment_timeframe' => $this->payment_timeframe,
                'payment_status' => $this->payment_status,
                'userId' => auth('admin')->user()->name,
                'repaymentDate' => $repaymentDate->format('Y-m-d'),
                'loanPaid' => 0,
                'loanBalance' => $this->convertToPhpNumber($this->price),
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
        $this->price = 0;
        $this->buyingDate = date('Y-m-d');
        $this->payment_timeframe = '';
        $this->payment_status = 1;
        $this->resetErrorBag();
    }

    /**
     * Convert a number from en-US locale to PHP number
     *
     * @param string $number
     * @return float
     */
    public function convertToPhpNumber($number)
    {
        return (float)str_replace(',', '', $number);
    }
}
