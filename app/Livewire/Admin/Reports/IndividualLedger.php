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

        // if($this->ledgers->count() > 0)
        //     $this->sendDispatchEvent();
        

        if($this->beginning_date == null)
            $this->beginning_date = date('Y-m-d');
        else
            $this->sendDispatchEvent();
        
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

    // #[On('on-downloadLedger')]
    public function downloadLedger($id, $beginning_date, $ending_date)
    {
        $ledgers = PaymentCapture::query()
            ->where('coopId', $id)
            ->whereBetween('paymentDate', [$beginning_date, $ending_date])
            ->orderBy('paymentDate', 'asc')
            ->get(['id', 'coopId', 'loanAmount', 'savingAmount', 'totalAmount', 'paymentDate', 'others', 'shareAmount', 'adminCharge']);
        if($ledgers->count() > 0){
            $memberId = Member::where('coopId', $id)->first();

            $isActive = ActiveLoans::where('coopId', $id)->first();
            $isOnLoan = ($isActive) ? true : false;

            $dataTotals = PaymentCapture::query()
                ->where('coopId', $id)
                ->whereBetween('paymentDate', [$beginning_date, $ending_date])
                ->selectRaw('sum("loanAmount") as loanAmount, sum("savingAmount") as savingAmount, sum("totalAmount") as totalAmount, sum("shareAmount") as shareAmount, sum("adminCharge") as adminCharge, sum("others") as others')
                ->first();

            $html = View::make('admin.reports.report_view', ['ledgers' => $ledgers, 
                'dataTotals' => $dataTotals, 'memberId' => $memberId, 'isOnLoan' => $isOnLoan,
                'beginning_date' => $beginning_date, 'ending_date' => $ending_date
            ]);

            $pdf = new Dompdf();
            $pdf->loadHtml($html->render(), 'UTF-8');
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();
            // $content = $pdf->output();

            $filename = str_replace(' ','_',$memberId->surname).'_'.$memberId->id.'.pdf';
            // file_put_contents(public_path($filename), $content);

            // $this->sendDispatchEvent();

            return $pdf->stream($filename);
        }else {
            abort(404);
        }    
    }
}
