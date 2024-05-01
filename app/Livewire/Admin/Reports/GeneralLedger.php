<?php

namespace App\Livewire\Admin\Reports;

use Dompdf\Dompdf;
use Livewire\Component;
use App\Models\PaymentCapture;
use Illuminate\Support\Facades\View;

class GeneralLedger extends Component
{
    public $beginning_date;
    public $ending_date;

    public function render()
    {
        $ledgers = PaymentCapture::query()
                ->whereBetween('paymentDate', [$this->beginning_date, $this->ending_date])
                ->selectRaw('coopId, sum(loanAmount) as loanAmount, sum(savingAmount) as savingAmount, sum(totalAmount) as totalAmount, sum(shareAmount) as shareAmount, sum(adminCharge) as adminCharge, sum(others) as others')
                ->groupBy('coopId')->get();

        // sum each of the columns in the $ledgers result collections
        $total_loan = $ledgers->sum('loanAmount') ?? 0;
        $total_saving = $ledgers->sum('savingAmount') ?? 0;
        $total_total = $ledgers->sum('totalAmount') ?? 0;
        $total_share = $ledgers->sum('shareAmount') ?? 0;
        $total_admin = $ledgers->sum('adminCharge') ?? 0;
        $total_others = $ledgers->sum('others') ?? 0;

        if($this->beginning_date == null)
            $this->beginning_date = date('Y-m-d');
        else
            $this->sendDispatchEvent();
        
        if($this->ending_date == null)
            $this->ending_date = date('Y-m-d');

        return view('livewire.admin.reports.general-ledger')->with(['ledgers' => $ledgers, 
            'total_loan' => $total_loan, 'total_saving' => $total_saving, 'total_total' => $total_total,
            'total_share' => $total_share, 'total_admin' => $total_admin, 'total_others' => $total_others]);
    }

    public function searchResult()
    {
        
        $this->sendDispatchEvent();
   
    }

    public function sendDispatchEvent()
    {
        $this->dispatch('on-openModal');
    }

    public function downloadLedger($beginning_date, $ending_date)
    {
        $ledgers = PaymentCapture::query()
                ->whereBetween('paymentDate', [$beginning_date, $ending_date])
                ->selectRaw('coopId, SUM(loanAmount) as loanAmount, SUM(savingAmount) as savingAmount, SUM(totalAmount) as totalAmount, SUM(shareAmount) as shareAmount, SUM(adminCharge) as adminCharge, SUM(others) as others')
                ->groupBy('coopId')->get();

        // sum each of the columns in the $ledgers result collections
        $total_loan = $ledgers->sum('loanAmount') ?? 0;
        $total_saving = $ledgers->sum('savingAmount') ?? 0;
        $total_total = $ledgers->sum('totalAmount') ?? 0;
        $total_share = $ledgers->sum('shareAmount') ?? 0;
        $total_admin = $ledgers->sum('adminCharge') ?? 0;
        $total_others = $ledgers->sum('others') ?? 0;



        if($ledgers->count() > 0){

            $html = View::make('admin.reports.generalexport_view', ['ledgers' => $ledgers, 
                'beginning_date' => $beginning_date, 'ending_date' => $ending_date, 'total_loan' => $total_loan,
                'total_saving' => $total_saving, 'total_total' => $total_total, 'total_share' => $total_share,
                'total_admin' => $total_admin, 'total_others' => $total_others
            ]);

            $pdf = new Dompdf();
            $pdf->loadHtml($html->render(), 'UTF-8');
            $pdf->setPaper('A4', 'landscape');
            $pdf->render();

            $filename = str_replace('/','_',$beginning_date.$ending_date).'_General_Report'.'.pdf';
            
            return $pdf->stream($filename);
        }else {
            abort(404, 'No record found');
        }    
    }
}
