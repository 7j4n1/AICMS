<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Member;
use App\Models\AnnualFee;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;

class AnnualFeeForm extends Form
{
    #[Validate('required|numeric|min:2023')]
    public $year;
    
    #[Validate('required')]
    public $amount;

    protected $messages = [
        'year.required' => 'The Year field is required.',
        'year.numeric' => 'The Year field must be a number.',
        'year.min' => 'The Year field must be at least 2023.',
        'amount.required' => 'The Annual-Fee Amount field is required.',
    ];

    public function boot()
    {
        $this->withValidator(function($validator){
            $validator->after(function($validator){
                $year = $this->year;
                $annual_fee = AnnualFee::where('annual_year', $year)->first();
                if($annual_fee)
                    $validator->errors()->add('year', 'Annual Fee already exists for the year.');

                if($year > date('Y'))
                    $validator->errors()->add('year', 'Year must be lesser than or equal to the current year.');

                if($this->convertToPhpNumber($this->amount) <= 0)
                    $validator->errors()->add('amount', 'Annual Fee Amount must be greater than 0.');
            });
        });
    }

    public function save()
    {
        try {
            $this->validate();

            DB::beginTransaction();
            // Optimized query
            $selected_members = Member::query()
                ->where('yearJoined', '<', $this->year)
                ->with(['payment_captures' => function ($query) {
                    $query->selectRaw('coopId, SUM(savingAmount) as total_savings')
                        ->groupBy('coopId');
                }])->get();
            
        
            
            foreach ($selected_members as $member) {
                $capture = $member->payment_captures->first();
                $savings = $capture->total_savings ?? 0;

                // Check if the member has already paid the annual fee
                $annual_fee = $member->annual_fees->first();
                $previous_annual_fee = $annual_fee->total_annual_fee ?? 0;
                
                $savings -= $previous_annual_fee;

                $amount_fee = $this->convertToPhpNumber($this->amount);

                
    
                AnnualFee::create([
                    'annual_year' => $this->year,
                    'annual_fee' => ($amount_fee < $savings) ? $amount_fee : ((-1) * $amount_fee),
                    'coopId' => $member->coopId,
                    'annual_savings' => $savings,
                    'total_savings' => $savings - (($amount_fee < $savings) ? $amount_fee : 0),
                    'userId' => auth('admin')->user()->name,
                ]);
    
            }

            DB::commit();

        } catch (\Throwable $th) {
            // dd($th->getMessage());
            DB::rollBack();

            throw $th;
            return false;
        }
        
        return true;

    }

    public function resetForm()
    {
        $this->year = date('Y');
        $this->amount = 0;
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
