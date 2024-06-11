<?php

namespace App\Livewire\Business\Reports;

use Livewire\Component;
use App\Models\ItemCapture;
use Livewire\Attributes\Computed;

class RepaymentAnnual extends Component
{
    public $beginDate;
    public $endDate;

    public function render()
    {
        $purchases = $this->getDetailsByDate();

        $total_paid = $purchases->sum('loanPaid') ?? 0;
        $total_balance = $purchases->sum('loanBalance') ?? 0;
        $total = $total_paid + $total_balance;

        return view('livewire.business.reports.repayment-annual')->with([
            'total_paid' => $total_paid,
            'total_balance' => $total_balance,
            'total' => $total,
            'csrf_token' => csrf_token(),
        ]);
    }

    public function mount()
    {
        $this->beginDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        // $this->sendDispatch();
    }

    #[Computed]
    public function itemcaptures()
    {
        // get all purchases made by the member
        return ItemCapture::query()
            ->whereBetween('buyingDate', [$this->beginDate, $this->endDate])
            ->orderBy('buyingDate', 'desc')
            ->get();
    }

    public function searchResult()
    {
        unset($this->itemcaptures);
        unset($this->getActiveStatusByDate);
        // unset($this->getInActiveStatusByDate);
        $this->sendDispatch();
    }

    public function getDetailsByDate()
    {
        return ItemCapture::query()
            ->whereBetween('buyingDate', [$this->beginDate, $this->endDate])
            ->orderBy('buyingDate', 'desc')
            ->get();
    }

    #[Computed]
    public function getActiveStatusByDate()
    {
        return ItemCapture::query()
            ->where('payment_status', 1)
            ->whereBetween('buyingDate', [$this->beginDate, $this->endDate])
            ->get()->count() ?? 0;
    }

    // #[Computed]
    // public function getInActiveStatusByDate()
    // {
    //     return ItemCapture::query()
    //         ->where('payment_status', 0)
    //         ->whereBetween('buyingDate', [$this->beginDate, $this->endDate])
    //         ->get()->count() ?? 0;
    // }

    public function sendDispatch()
    {
        $this->dispatch('table-show');
    }

}
