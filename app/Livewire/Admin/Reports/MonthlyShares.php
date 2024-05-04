<?php

namespace App\Livewire\Admin\Reports;

use Livewire\Component;
use App\Models\PaymentCapture;
use App\Models\PreviousLedger2023;
use Illuminate\Support\Facades\DB;

class MonthlyShares extends Component
{
    public $year;
    public $month_day;
    private $myshares;
    public function render()
    {
        // query to get the sum of shares for each month based on given year
        
        
        if($this->year == "2023" || $this->year == 2023){
            
            $this->myshares = PreviousLedger2023::query()
                ->whereYear('paymentDate', $this->year)
                ->selectRaw('MONTH(paymentDate) as month, sum(shareAmount) as shareAmount')
                ->groupBy('month')->get();
        }else {
            $this->myshares = PaymentCapture::query()
                ->whereYear('paymentDate', $this->year)
                ->selectRaw('MONTH(paymentDate) as month, sum(shareAmount) as shareAmount')
                ->groupBy('month')->get();
        }
        
        $total_shares = $this->myshares->sum('shareAmount') ?? 0;
        

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
