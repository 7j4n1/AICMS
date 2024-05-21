<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\PaymentCapture; // Add this line
use Illuminate\Support\Facades\DB;

class MonthlyShares extends Component
{
    public $year;
    public $month_day;
    private $myshares;

    public function render()
    {
        
        // query to get the sum of shares for each month based on given year group by coopId
        // get unique coopId

            // $this->myshares = PaymentCapture::query()
            //     ->selectRaw('MONTH(paymentDate) as month, sum(shareAmount) as shareAmount')
            //     ->groupBy('coopId')
            //     ->groupBy('month')->limit(10);

        $this->myshares = PaymentCapture::query()
            ->select(
                DB::raw('coopId'), 
                DB::raw('SUM(shareAmount) as shareAmount'),
                DB::raw('MONTH(paymentDate) as month')
            )
            ->whereYear('paymentDate', $this->year)
            ->groupBy('coopId', 'month')
            ->get()->groupBy('coopId');

        
        // foreach ($this->myshares as $share) {
        //     $this->allshares[$share->coopId][] = [
        //         'coopId' => $share->coopId,
        //         'shareAmount' => $share->shareAmount,
        //         'month' => $share->month
        //     ];
        // }
        
        
        $total_shares = PaymentCapture::query()
            ->whereYear('paymentDate', $this->year)
            ->sum('shareAmount') ?? 0;
        

        if($this->year == null)
            $this->year = date('Y');
        else
            $this->sendDispatchEvent();

        $this->month_day = [
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        ];

        return view('livewire.admin.reports.monthly-shares')
            ->with(['shares' => $this->myshares, 'total_shares' => $total_shares, 'year' => $this->year, 'month_day' => $this->month_day]);
    }

    public function searchResult()
    {
        
        $this->sendDispatchEvent();
   
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }
}
