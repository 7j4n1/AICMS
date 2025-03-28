<?php

namespace App\Http\Controllers\Api;

use App\Models\Member;
use App\Models\ActiveLoans;
use Illuminate\Http\Request;
use App\Models\PaymentCapture;
use App\Http\Controllers\Controller;

class MemberRecordsController extends Controller
{
    // get Balance function
    public function getBalance()
    {
        // get the user
        $user = auth('api')->user();
        $coopId = $user->coopId;

        // Get data using the user model
        // $member = Member::where('coopId', $coopId)->first();
        $payments = PaymentCapture::where('coopId', $coopId)->latest()->get();
        $loans = ActiveLoans::where('coopId', $coopId)->first();

        // Calculate balances using the existing logic
        $loanBalance = $loans->loanBalance;
        $sharesBalance = $payments->sum('shareAmount');
        $savingsBalance = $payments->sum('savingAmount');
        $totalBalance = $savingsBalance + $sharesBalance + $loanBalance;
        

        return response()->json([
            'savings' => $savingsBalance,
            'shares' => $sharesBalance,
            'loan' => $loanBalance,
            'total_balance' => $totalBalance
        ]);
    }

    // get savings records function
    public function getSavingsRecords()
    {
        // get the user
        $user = auth('api')->user();
        $coopId = $user->coopId;

        // Get data using the user model
        $payments = PaymentCapture::where('coopId', $coopId)
            ->where('savingAmount', '>', 0)
            ->orderByDesc('paymentDate')
            ->get(['id', 'paymentDate', 'savingsAmount']);

        return response()->json([
            'status' => 'success',
            'message' => 'Savings records fetched successfully',
            'data' => $payments
        ]);
    }

    // get shares records function
    public function getSharesRecords()
    {
        // get the user
        $user = auth('api')->user();
        $coopId = $user->coopId;

        // Get data using the user model
        $payments = PaymentCapture::where('coopId', $coopId)
            ->where('shareAmount', '>', 0)
            ->orderByDesc('paymentDate')
            ->get(['id', 'paymentDate', 'sharesAmount']);

        return response()->json([
            'status' => 'success',
            'message' => 'Shares records fetched successfully',
            'data' => $payments
        ]);
    }

    // get loan repayment records function
    public function getLoanRepaymentRecords(Request $request)
    {
        // get the user
        $user = auth('api')->user();
        $coopId = $user->coopId;

        // Check if the user has an active loan
        $loan = ActiveLoans::where('coopId', $coopId)->first();
        
        $payments = [];

        if ($loan) {
            // Get data using the user model
            $loanDate = $loan->loanDate;

            // Get data using the user model
            // Retrieve only valid loan date repayments
            $payments = PaymentCapture::where('coopId', $coopId)
                ->where('loanAmount', '>', 0)
                ->where('paymentDate', '>=', $loanDate)
                ->orderByDesc('paymentDate')
                ->get(['id', 'paymentDate', 'loanAmount']);
        }

        

        return response()->json([
            'status' => 'success',
            'message' => 'Loan repayment records fetched successfully',
            'data' => $payments
        ]);
    }
}
