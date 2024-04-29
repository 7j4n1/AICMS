<?php

namespace App\Livewire\Admin;

use App\Models\ActiveLoans;
use App\Models\Member;
use Livewire\Component;
use App\Models\PaymentCapture;
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
        ->selectRaw('SUM(payment_captures.totalAmount) as total_amount, SUM(payment_captures.savingAmount) as total_savings, SUM(payment_captures.loanAmount) as total_loans')
        ->first();


        if ($totals) {
            $this->total_amounts = $totals->total_amount;
            $this->total_savings = $totals->total_savings;
            
            $this->total_members = Member::count();
            $this->total_shares = PaymentCapture::sum('shareAmount');
        }

        $members = Member::query()
            ->orderBy('coopId', 'desc')
            ->paginate(10);

        $loans = ActiveLoans::query()
            ->orderBy('lastPaymentDate', 'desc')
            ->paginate(10);
        
        if($loans->count() > 0)
            $this->total_loans = ActiveLoans::sum('loanAmount');
        else
            $this->total_loans = 0;

        return view('livewire.admin.dashboard')
            ->with(['session' => session(), 'members' => $members, 'loans' => $loans]);
    }
}
