<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PaymentNotificationUpload;

class PaymentNotificationController extends Controller
{
    // store payment notification function
    public function store(Request $request)
    {
        // validate the request
        $request->validate([
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'payment_time' => 'required',
            'bank_used' => 'required|string',
            'payment_channel' => 'required|in:Bank deposit,USSD,Internet banking,ATM transfer,POS transfer,Mobile app',
            'depositor_name' => 'required|string',
            'reference_number' => 'nullable|string',
            'additional_details' => 'nullable|string',
            'evidence' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048'
        ]);

        $user = $request->user();
        $coopId = $user->coopId;

        $uploadPath = null;

        // upload the evidence file if it exists
        if ($request->hasFile('evidence')) {
            $file = $request->file('evidence');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $uploadPath = $file->storeAs('public/evidence', $fileName);
        }

        // create the payment notification
        $paymentNotification = new PaymentNotificationUpload();
        $paymentNotification->coopId = $coopId;
        $paymentNotification->amount = $request->amount;
        $paymentNotification->payment_date = $request->payment_date;
        $paymentNotification->payment_time = $request->payment_time;
        $paymentNotification->bank_used = $request->bank_used;
        $paymentNotification->payment_channel = $request->payment_channel;
        $paymentNotification->depositor_name = $request->depositor_name;
        $paymentNotification->reference_number = $request->reference_number;
        $paymentNotification->additional_details = $request->additional_details;
        $paymentNotification->status = 'pending';
        $paymentNotification->evidence_path = $uploadPath;
        $paymentNotification->save();

        return response()->json([
            'status' => 'success',
            'data' => $paymentNotification
        ]);
    }
}
