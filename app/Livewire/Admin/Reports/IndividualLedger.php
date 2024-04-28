<?php

namespace App\Livewire\Admin\Reports;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\Member;
use Livewire\Component;
use App\Models\ActiveLoans;
use Livewire\Attributes\On;
use App\Models\PaymentCapture;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class IndividualLedger extends Component
{
    public $beginning_date;
    public $ending_date;
    public $coopId;
    public $isFound = false;
    public $dataTotals;
    public $ledgers;

    public function render()
    {
        $memberIds = Member::orderBy("coopId","asc")->get(['coopId']);

        $this->ledgers = PaymentCapture::query()
            ->where('coopId', $this->coopId)
            ->whereBetween('paymentDate', [$this->beginning_date, $this->ending_date])
            ->orderBy('paymentDate', 'asc')
            ->get(['id', 'coopId', 'loanAmount', 'savingAmount', 'totalAmount', 'paymentDate', 'others', 'shareAmount', 'adminCharge']);

        if($this->ledgers->count() > 0)
            $this->isFound = true;
        else
            $this->isFound = false;
        

        if($this->beginning_date == null)
            $this->beginning_date = date('Y-m-d');
        
        if($this->ending_date == null)
            $this->ending_date = date('Y-m-d');

        
        return view('livewire.admin.reports.individual-ledger')->with(['ledgers' => $this->ledgers, 'session' => session(), 'memberIds' => $memberIds]);
    }

    public function mount()
    {
        
    }
    public function searchResult()
    {
        
        $this->sendDispatchEvent();
   
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }

    #[On('on-downloadLedger')]
    public function downloadLedger($id)
    {
        $memberId = Member::where('coopId', $this->coopId)->first();
        $isActive = ActiveLoans::where('coopId', $this->coopId)->first();
        $isOnLoan = ($isActive) ? true : false;

        $this->dataTotals = PaymentCapture::query()
            ->where('coopId', $this->coopId)
            ->whereBetween('paymentDate', [$this->beginning_date, $this->ending_date])
            ->selectRaw('sum(loanAmount) as loanAmount, sum(savingAmount) as savingAmount, sum(totalAmount) as totalAmount, sum(shareAmount) as shareAmount, sum(adminCharge) as adminCharge, sum(others) as others')
            ->first();

    //     $pdf = Pdf::loadView('admin.reports.report_view', ['ledgers' => $this->ledgers, 
    //         'dataTotals' => $this->dataTotals, 'memberId' => $memberId, 'isOnLoan' => $isOnLoan,
    //         'beginning_date' => $this->beginning_date, 'ending_date' => $this->ending_date
    // ]);
        $html = View::make('admin.reports.report_view', ['ledgers' => $this->ledgers, 
        'dataTotals' => $this->dataTotals, 'memberId' => $memberId, 'isOnLoan' => $isOnLoan,
        'beginning_date' => $this->beginning_date, 'ending_date' => $this->ending_date
    ]);
        // dd($html->render());
        $pdf = new Dompdf();
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        $content = $pdf->output();
        file_put_contents(public_path($memberId->id.'.pdf'), $content);

        return response()->download(public_path($memberId->id.'.pdf'));
        
    }
}
