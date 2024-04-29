<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\PaymentCapture;

class MonthlyShares extends Component
{
    public $year;
    public $month_day;
    public function render()
    {
        // query to get the sum of shares for each month based on given year
        $shares = PaymentCapture::query()
                ->whereYear('paymentDate', $this->year)
                ->selectRaw('MONTH(paymentDate) as month, sum(shareAmount) as shareAmount')
                ->groupBy('month')->get();

        if($shares->count() > 0){
            $total_shares = $shares->sum('shareAmount') ?? 0;
        }else {
            $total_shares = 0;
        }
        

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
            ->with(['shares' => $shares, 'total_shares' => $total_shares, 'year' => $this->year, 'month_day' => $this->month_day]);
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
