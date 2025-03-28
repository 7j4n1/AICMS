<?php

namespace App\Http\Controllers\Api;

use Dompdf\Dompdf;
use App\Models\Member;
use App\Models\ActiveLoans;
use Illuminate\Http\Request;
use App\Models\PaymentCapture;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\View;

class ReportController extends Controller
{
    public function downloadLedger(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            // 'type' => 'required|in:savings,shares,loan',
            'format' => 'required|in:pdf,excel'
        ]);

        $user = $request->user('admin');
        $coopId = $user->coopId;

        $beginning_date = $request->start_date;
        $ending_date = $request->end_date;

        // get the ledgers
        $ledgers = PaymentCapture::query()
            ->where('coopId', $coopId)
            ->whereBetween('paymentDate', [$beginning_date, $ending_date])
            ->orderByDesc('paymentDate')
            ->get();

        if($ledgers)
        {
            $memberId = Member::where('coopId', $coopId)->first();

            // check if the user is on loan
            $isActive = ActiveLoans::where('coopId', $coopId)->first();
            $isOnLoan = ($isActive) ? true : false;

            // get the loan balance
            $loanBalance = ($isActive) ? $isActive->loanBalance : 0;

            // get the sum of each payment savings
            $loan_paid = $ledgers->sum('loanAmount') ?? 0;
            $total_saving = $ledgers->sum('savingAmount') ?? 0;
            $total_total = $ledgers->sum('totalAmount') ?? 0;
            $total_share = $ledgers->sum('shareAmount') ?? 0;
            $total_admin = $ledgers->sum('adminCharge') ?? 0;
            $total_others = $ledgers->sum('others') ?? 0;
            // $total_loan = $balance; // get the loan balance


            // set the view html
            $htmlView = View::make('admin.reports.report_view', ['ledgers' => $ledgers, 
                'memberId' => $memberId, 'isOnLoan' => $isOnLoan,
                'beginning_date' => $beginning_date, 'ending_date' => $ending_date,
                'loan_paid' => $loan_paid, 'total_saving' => $total_saving, 'total_total' => $total_total,
                'total_share' => $total_share, 'total_admin' => $total_admin, 'total_others' => $total_others,
                'loan_balance' => $loanBalance
            ]);

            $pdf = new Dompdf();
            $pdf->loadHtml($htmlView->render(), 'UTF-8');
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            // file name
            $filename = str_replace(' ','_',$memberId->surname).'_'.$memberId->id.Date::now()->format('Ymd').'.pdf';

            // check the format
            if($request->format == 'pdf')
            {
                return $pdf->stream($filename);
            }
            else
            {
                // return the excel file
                
            }
        }


    }
}
