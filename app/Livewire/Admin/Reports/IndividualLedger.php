<?php

namespace App\Livewire\Admin\Reports;

use Dompdf\Dompdf;
use App\Models\Member;
use Livewire\Component;
use App\Models\ActiveLoans;
use App\Models\PaymentCapture;
use Illuminate\Support\Facades\View;
class IndividualLedger extends Component
{
    public $beginning_date;
    public $ending_date;
    public $coopId;
    public $csrf_token;

    public function render()
    {
        // check if the autentcated user is a member or an admin
        // if the authenticated user is an admin, get all the memberIds
        // else get only the authenticated user's coopId
        
        $memberIds = Member::orderBy("coopId","asc")->get(['coopId']);
        $ledgers = PaymentCapture::query()
            ->where('coopId', $this->coopId)
            ->whereBetween('paymentDate', [$this->beginning_date, $this->ending_date])
            ->orderByDesc('paymentDate')
            ->get();
            
        $user = auth('admin')->check() && auth('admin')->user()->hasRole(['member'], 'admin');


        if($user){
            $this->coopId = auth('admin')->user()->coopId;
            // set the beginning to the first day of the month if null
            $this->beginning_date = ($this->beginning_date == null) ? date('Y-m-01') : $this->beginning_date;
            // set the ending to the last day of the month if null
            $this->ending_date = ($this->ending_date == null) ? date('Y-m-d') : $this->ending_date;
            $ledgers = PaymentCapture::query()
                ->where('coopId', $this->coopId)
                ->whereBetween('paymentDate', [$this->beginning_date, $this->ending_date])
                ->orderByDesc('paymentDate')
                ->get();
        }

        // sum each of the columns in the $ledgers result collections
        $total_saving = $ledgers->sum('savingAmount') ?? 0;
        $total_total = $ledgers->sum('totalAmount') ?? 0;
        $total_share = $ledgers->sum('shareAmount') ?? 0;
        $total_admin = $ledgers->sum('adminCharge') ?? 0;
        $total_others = $ledgers->sum('others') ?? 0;

        $isActive = ActiveLoans::where('coopId', $this->coopId)->first();

        $total_loan = ($isActive) ? $isActive->loanBalance : 0;
        

        if($this->beginning_date == null)
            $this->beginning_date = date('Y-m-d');
        
        if($this->ending_date == null)
            $this->ending_date = date('Y-m-d');

        
        return view('livewire.admin.reports.individual-ledger')->with(['ledgers' => $ledgers, 'session' => session(), 'memberIds' => $memberIds,
        'total_loan' => $total_loan, 'total_saving' => $total_saving, 'total_total' => $total_total,
        'total_share' => $total_share, 'total_admin' => $total_admin, 'total_others' => $total_others,
        'csrf_token' => $this->csrf_token
        ]);
    }

    public function mount()
    {
        $this->csrf_token = csrf_token();
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
            ->orderByDesc('paymentDate')
            ->get(['id', 'coopId', 'loanAmount', 'savingAmount', 'totalAmount', 'paymentDate', 'others', 'shareAmount', 'adminCharge']);
        if($ledgers){
            $memberId = Member::where('coopId', $id)->first();

            $isActive = ActiveLoans::where('coopId', $id)->first();
            $isOnLoan = ($isActive) ? true : false;

            $balance = ($isActive) ? $isActive->loanBalance : 0;

            $dataTotals = PaymentCapture::query()
                ->where('coopId', $id)
                ->whereBetween('paymentDate', [$beginning_date, $ending_date])
                ->get();

            // ->selectRaw('SUM("loanAmount") as loanAmount, SUM("savingAmount") as savingAmount, SUM("totalAmount") as totalAmount, SUM("shareAmount") as shareAmount, SUM("adminCharge") as adminCharge, SUM("others") as others')

            $total_loan = $dataTotals->sum('loanAmount') ?? 0;
            $total_saving = $dataTotals->sum('savingAmount') ?? 0;
            $total_total = $dataTotals->sum('totalAmount') ?? 0;
            $total_share = $dataTotals->sum('shareAmount') ?? 0;
            $total_admin = $dataTotals->sum('adminCharge') ?? 0;
            $total_others = $dataTotals->sum('others') ?? 0;

            $html = View::make('admin.reports.report_view', ['ledgers' => $ledgers, 
                'dataTotals' => $dataTotals, 'memberId' => $memberId, 'isOnLoan' => $isOnLoan,
                'beginning_date' => $beginning_date, 'ending_date' => $ending_date,
                'total_loan' => $total_loan, 'total_saving' => $total_saving, 'total_total' => $total_total,
                'total_share' => $total_share, 'total_admin' => $total_admin, 'total_others' => $total_others,
                'balance' => $balance
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
