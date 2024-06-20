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
        $user = auth('admin')->check() && auth('admin')->user()->hasRole(['member'], 'admin');


        $totals = PaymentCapture::query()
        ->selectRaw('SUM(totalAmount) as total_amount, SUM(savingAmount) as total_savings, SUM(loanAmount) as total_loans, SUM(shareAmount) as total_shares')
        ->first();

        if($user){
            $totals = PaymentCapture::query()
                ->where('coopId', auth('admin')->user()->coopId)
                    ->selectRaw('SUM(totalAmount) as total_amount, SUM(savingAmount) as total_savings, SUM(loanAmount) as total_loans, SUM(shareAmount) as total_shares')
                        ->first();
        }

        $this->total_amounts = $totals->total_amount ?? 0;
        $this->total_savings = $totals->total_savings ?? 0;
        
        $this->total_members = Member::count();
        $this->total_shares = $totals->total_shares ?? 0;

        $members = Member::query()
            ->orderBy('coopId', 'desc')
            ->limit(10)->get();

        $loans = ActiveLoans::query()
            ->orderBy('lastPaymentDate', 'desc')
            ->limit(10)->get();
        
        $this->total_loans = ActiveLoans::sum('loanAmount') ?? 0;
        
        if($user){
            $this->total_loans = ActiveLoans::query()
                ->where('coopId', auth('admin')->user()->coopId)
                    ->first()->loanBalance ?? 0;
            $loans = ActiveLoans::query()
                ->where('coopId', auth('admin')->user()->coopId)
                        ->get(['*']);
        }

        return view('livewire.admin.dashboard')
            ->with(['session' => session(), 'members' => $members, 'loans' => $loans]);
    }
}
