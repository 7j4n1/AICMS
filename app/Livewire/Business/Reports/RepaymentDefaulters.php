<?php

namespace App\Livewire\Business\Reports;

use App\Models\ItemCapture;
use Carbon\Carbon;
use Livewire\Component;

class RepaymentDefaulters extends Component
{
    public function render()
    {

        $result_query = $this->checkItemDefaulters();

        return view('livewire.business.reports.repayment-defaulters')->with([
            'defaulters' => $result_query[0],
            'total_balance' => $result_query[1],
            'total_items' => $result_query[2],
        ]);
    }

    private function checkItemDefaulters()
    {
        $currentDate = Carbon::now();
        $minConsequtiveDefault = 2; // Minimum number of months a member can default before being tagged as a defaulter



        $items = ItemCapture::query()
            ->where('payment_status', 1) // Filter ongoing items only
            ->where('repaymentDate', '<', $currentDate->format('Y-m-d'))
            ->orderBy('repaymentDate', 'asc')
            ->get();
        
        $total_balance = 0;
        $total_items = 0;
        $defaulters_list = [];

        // get the list of defaulters where the repaymentDate is less than the current date
        // and tag members on active item as defaulter if He/she refuse to pay two/three/four/five/..... months consecutively.
        
        foreach ($items as $item) {
            $repaymentDate = Carbon::parse($item->repaymentDate);
            $diffInMonths = $currentDate->diffInMonths($repaymentDate);
            

            if ($diffInMonths >= $minConsequtiveDefault) {
                $balance = $item->itemBalance ?? 0;
                $defaulters_list[] = [
                    'purchase' => $item,
                    'diff' => $diffInMonths,
                    'balance' => $balance,
                ];
                
                $total_balance += $balance;
                $total_items += $item->itemAmount;
            }

        }

        // sort the defaulters list based on the diff in ascending order
        usort($defaulters_list, function ($a, $b) {
            return $a['diff'] <=> $b['diff'];
        });

        return [$defaulters_list, $total_balance, $total_items];
        
    }
}
