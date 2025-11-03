<?php

namespace App\Livewire\Admin\Reports;

use Dompdf\Dompdf;
use Livewire\Component;
use App\Models\Member;
use App\Models\PaymentCapture;
use App\Models\PreviousLedger2023;
use Illuminate\Support\Facades\View;

class GroupReport extends Component
{
    public $beginning_date;
    public $ending_date;
    public $group_from;
    public $group_to;

    public function render()
    {
        $ledgers = collect();
        $preledgers = collect();

        // Initialize totals
        $total_loan = 0;
        $total_saving = 0;
        $total_total = 0;
        $total_share = 0;
        $total_admin = 0;
        $total_others = 0;

        // If group filter is set, get members in that group range
        if ($this->group_from || $this->group_to) {
            $groupFrom = $this->group_from ?? 1;
            $groupTo = $this->group_to ?? $groupFrom;

            // Get members in the specified group range
            $memberCoopIds = Member::whereBetween('groupId', [$groupFrom, $groupTo])
                ->pluck('coopId')
                ->toArray();

            if (!empty($memberCoopIds) && $this->beginning_date && $this->ending_date) {
                $ledgers = PaymentCapture::query()
                    ->whereIn('coopId', $memberCoopIds)
                    ->whereBetween('paymentDate', [$this->beginning_date, $this->ending_date])
                    ->selectRaw('coopId, sum(loanAmount) as loanAmount, sum(savingAmount) as savingAmount, sum(totalAmount) as totalAmount, sum(shareAmount) as shareAmount, sum(adminCharge) as adminCharge, sum(others) as others')
                    ->groupBy('coopId')->get();

                $preledgers = PreviousLedger2023::query()
                    ->whereIn('coopId', $memberCoopIds)
                    ->selectRaw('coopId, sum(loanAmount) as loanAmount, sum(savingAmount) as savingAmount, sum(totalAmount) as totalAmount, sum(shareAmount) as shareAmount, sum(adminCharge) as adminCharge, sum(others) as others')
                    ->groupBy('coopId')->get();
            }
        } elseif ($this->beginning_date && $this->ending_date) {
            // No group filter, show all
            $ledgers = PaymentCapture::query()
                ->whereBetween('paymentDate', [$this->beginning_date, $this->ending_date])
                ->selectRaw('coopId, sum(loanAmount) as loanAmount, sum(savingAmount) as savingAmount, sum(totalAmount) as totalAmount, sum(shareAmount) as shareAmount, sum(adminCharge) as adminCharge, sum(others) as others')
                ->groupBy('coopId')->get();

            $preledgers = PreviousLedger2023::query()
                ->selectRaw('coopId, sum(loanAmount) as loanAmount, sum(savingAmount) as savingAmount, sum(totalAmount) as totalAmount, sum(shareAmount) as shareAmount, sum(adminCharge) as adminCharge, sum(others) as others')
                ->groupBy('coopId')->get();
        }

        // Calculate totals
        $total_loan = $ledgers->sum('loanAmount') ?? 0;
        $total_saving = $ledgers->sum('savingAmount') ?? 0;
        $total_total = $ledgers->sum('totalAmount') ?? 0;
        $total_share = $ledgers->sum('shareAmount') ?? 0;
        $total_admin = $ledgers->sum('adminCharge') ?? 0;
        $total_others = $ledgers->sum('others') ?? 0;

        $total_loan += $preledgers->sum('loanAmount') ?? 0;
        $total_saving += $preledgers->sum('savingAmount') ?? 0;
        $total_total += $preledgers->sum('totalAmount') ?? 0;
        $total_share += $preledgers->sum('shareAmount') ?? 0;
        $total_admin += $preledgers->sum('adminCharge') ?? 0;
        $total_others += $preledgers->sum('others') ?? 0;

        if($this->beginning_date == null)
            $this->beginning_date = date('Y-m-d');
        else
            $this->sendDispatchEvent();
        
        if($this->ending_date == null)
            $this->ending_date = date('Y-m-d');

        return view('livewire.admin.reports.group-report')->with(['ledgers' => $ledgers, 
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

    public function downloadLedger($beginning_date, $ending_date, $group_from = null, $group_to = null)
    {
        $groupFrom = $group_from ?? 1;
        $groupTo = $group_to ?? $groupFrom;

        // Get members in the specified group range
        $memberCoopIds = Member::whereBetween('groupId', [$groupFrom, $groupTo])
            ->pluck('coopId')
            ->toArray();

        if (empty($memberCoopIds)) {
            abort(404, 'No members found in the specified group range');
        }

        $ledgers = PaymentCapture::query()
                ->whereIn('coopId', $memberCoopIds)
                ->whereBetween('paymentDate', [$beginning_date, $ending_date])
                ->selectRaw('coopId, SUM(loanAmount) as loanAmount, SUM(savingAmount) as savingAmount, SUM(totalAmount) as totalAmount, SUM(shareAmount) as shareAmount, SUM(adminCharge) as adminCharge, SUM(others) as others')
                ->groupBy('coopId')
                ->get();

        $total_loan = $ledgers->sum('loanAmount') ?? 0;
        $total_saving = $ledgers->sum('savingAmount') ?? 0;
        $total_total = $ledgers->sum('totalAmount') ?? 0;
        $total_share = $ledgers->sum('shareAmount') ?? 0;
        $total_admin = $ledgers->sum('adminCharge') ?? 0;
        $total_others = $ledgers->sum('others') ?? 0;

        if($ledgers->count() > 0){

            $html = View::make('admin.reports.groupexport_view', ['ledgers' => $ledgers, 
                'beginning_date' => $beginning_date, 'ending_date' => $ending_date, 'total_loan' => $total_loan,
                'total_saving' => $total_saving, 'total_total' => $total_total, 'total_share' => $total_share,
                'total_admin' => $total_admin, 'total_others' => $total_others,
                'group_from' => $groupFrom, 'group_to' => $groupTo
            ]);

            $pdf = new Dompdf();
            $pdf->loadHtml($html->render(), 'UTF-8');
            $pdf->setPaper('A4', 'landscape');
            $pdf->render();

            $filename = 'Group_'.$groupFrom.'_to_'.$groupTo.'_Report_'.str_replace('/','-',$beginning_date).'_'.str_replace('/','-',$ending_date).'.pdf';
            
            return $pdf->stream($filename);
        }else {
            abort(404, 'No record found for the specified groups');
        }    
    }
}
