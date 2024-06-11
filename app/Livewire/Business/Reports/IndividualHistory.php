<?php

namespace App\Livewire\Business\Reports;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\ItemCapture;

class IndividualHistory extends Component
{
    public $coopId;
    public $beginDate;
    public $endDate;


    public function render()
    {
        $purchase = $this->getDetailsByCoopId();

        $total_paid = $purchase->sum('loanPaid') ?? 0;
        $total_balance = $purchase->sum('loanBalance') ?? 0;
        $total = $total_paid + $total_balance;

        $status = ItemCapture::query()-> where('coopId', $this->coopId)
            ->where('payment_status', 1)->first();

        return view('livewire.business.reports.individual-history')->with([
            'purchase' => $status,
            'total_paid' => $total_paid,
            'total_balance' => $total_balance,
            'total' => $total,
            'csrf_token' => csrf_token(),
        ]);
    }

    public function mount()
    {
        $user = auth('admin')->check() && auth('admin')->user()->hasRole(['member'], 'admin');
        if($user){
            $this->coopId = auth('admin')->user()->coopId;
        }
        $this->beginDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        // $this->sendDispatch();
    }

    #[Computed]
    public function itemcaptures()
    {
        $user = auth('admin')->check() && auth('admin')->user()->hasRole(['member'], 'admin');
        if($user){
            $this->coopId = auth('admin')->user()->coopId;
            // set the beginning to the first day of the month if null
            $this->beginDate = ($this->beginDate == null) ? date('Y-m-01') : $this->beginDate;
            // set the ending to the last day of the month if null
            $this->endDate = ($this->endDate == null) ? date('Y-m-d') : $this->endDate;
            
        }
        // get all purchases made by the member
        return ItemCapture::query()
            ->where('coopId', $this->coopId)
            ->whereBetween('buyingDate', [$this->beginDate, $this->endDate])
            ->orderBy('buyingDate', 'desc')
            ->get();
    }

    #[Computed]
    public function memberIds()
    {
        // get distinct values of coopId from item_captures
        return ItemCapture::distinct()
            ->orderBy('coopId', 'asc')
            ->get(['coopId']);
    }

    public function getDetailsByCoopId()
    {
        return ItemCapture::query()
            ->where('coopId', $this->coopId)
            ->whereBetween('buyingDate', [$this->beginDate, $this->endDate])
            ->orderBy('buyingDate', 'desc')
            ->get();
    }

    public function searchResult()
    {
        unset($this->itemcaptures);
        $this->sendDispatch();
    }

    public function sendDispatch()
    {
        $this->dispatch('table-show');
    }

}
