<?php

namespace App\Livewire\Business\Reports;

use Livewire\Component;
use App\Models\ItemCapture;
use Livewire\Attributes\Computed;

class ActivePurchases extends Component
{
    public function render()
    {
        $purchase = $this->getDetailsByCoopId();

        $total_paid = $purchase->sum('loanPaid') ?? 0;
        $total_balance = $purchase->sum('loanBalance') ?? 0;
        $total = $total_paid + $total_balance;
        $counter = $purchase->count();

        return view('livewire.business.reports.active-purchases')->with([
            'no_actives' => $counter,
            'total_paid' => $total_paid,
            'total_balance' => $total_balance,
            'total' => $total
        ]);
    }

    #[Computed]
    public function itemcaptures()
    {
        // get all purchases made by the members
        return ItemCapture::query()
            ->where('payment_status', 1)
            ->where('repaymentDate', '>=', now()->format('Y-m-d'))
            ->orderBy('buyingDate', 'desc')
            ->get();
    }

    public function getDetailsByCoopId()
    {
        return ItemCapture::query()
            ->where('payment_status', 1)
            ->where('repaymentDate', '>=', now()->format('Y-m-d'))
            ->orderBy('buyingDate', 'desc')
            ->get();
    }

}
