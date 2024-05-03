<?php

namespace App\Livewire\Admin;

use App\Models\ActiveLoans;
use App\Models\Member;
use Livewire\Component;
use App\Models\PaymentCapture;
use App\Models\PreviousLedger2023;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;
    public $total_amounts = 0;
    public $total_savings = 0;
    public $total_loans = 0;
    public $total_members = 0;
    public $total_shares = 0;
    

    protected $paginationTheme = "bootstrap";

    public function render()
    {

        $totals = PaymentCapture::query()
        ->selectRaw('SUM(totalAmount) as total_amount, SUM(savingAmount) as total_savings, SUM(loanAmount) as total_loans, SUM(shareAmount) as total_shares')
        ->first();

        $pretotals = PreviousLedger2023::query()
        ->selectRaw('SUM(totalAmount) as total_amount, SUM(savingAmount) as total_savings, SUM(loanAmount) as total_loans, SUM(shareAmount) as total_shares')
        ->first();

        $pretotal_amounts = $pretotals->total_amount ?? 0;
        $pretotal_savings = $pretotals->total_savings ?? 0;
        $pretotal_shares = $pretotals->total_shares ?? 0;

        // if ($totals) {
            $this->total_amounts = $totals->total_amount ?? 0;
            $this->total_savings = $totals->total_savings ?? 0;
            
            $this->total_members = Member::count();
            $this->total_shares = $totals->total_shares ?? 0;

            $this->total_amounts += $pretotal_amounts;
            $this->total_savings += $pretotal_savings;
            $this->total_shares += $pretotal_shares;
        // }

        $members = Member::query()
            ->orderBy('coopId', 'desc')
            ->limit(10)->get();

        $loans = ActiveLoans::query()
            ->orderBy('lastPaymentDate', 'desc')
            ->limit(10)->get();
        
        $this->total_loans = ActiveLoans::sum('loanAmount') ?? 0;

        return view('livewire.admin.dashboard')
            ->with(['session' => session(), 'members' => $members, 'loans' => $loans]);
    }
}
