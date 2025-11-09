<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PaymentNotificationUpload;
use Illuminate\Support\Facades\Validator;

class PaymentNotificationController extends Controller
{
    /**
     * Get all payment notifications (admin sees all, member sees their own)
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $status = $request->input('status');
        $user = $request->user();

        $query = PaymentNotificationUpload::with('member');

        // If user is a member, only show their notifications
        if ($user->coopId && !$user->role) {
            $query->where('coopId', $user->coopId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $notifications->items(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'last_page' => $notifications->lastPage()
            ]
        ]);
    }

    /**
     * Get a specific payment notification
     */
    public function show(Request $request, $id)
    {
        $notification = PaymentNotificationUpload::with('member')->find($id);

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment notification not found'
            ], 404);
        }

        $user = $request->user();

        // Check if user has access
        if ($user->coopId && !$user->role && $notification->coopId != $user->coopId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $notification
        ]);
    }

    /**
     * Store payment notification function
     */
    public function store(Request $request)
    {
        // validate the request
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

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
            'message' => 'Payment notification submitted successfully',
            'data' => $paymentNotification
        ], 201);
    }

    /**
     * Approve a payment notification
     */
    public function approve(Request $request, $id)
    {
        $notification = PaymentNotificationUpload::find($id);

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment notification not found'
            ], 404);
        }

        if ($notification->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment notification has already been processed'
            ], 422);
        }

        $user = $request->user();

        $notification->update([
            'status' => 'approved',
            'approved_by' => $user->name,
            'approved_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment notification approved successfully',
            'data' => $notification
        ]);
    }

    /**
     * Reject a payment notification
     */
    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $notification = PaymentNotificationUpload::find($id);

        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment notification not found'
            ], 404);
        }

        if ($notification->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment notification has already been processed'
            ], 422);
        }

        $user = $request->user();

        $notification->update([
            'status' => 'rejected',
            'rejected_by' => $user->name,
            'rejected_at' => now(),
            'rejected_reason' => $request->reason,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment notification rejected successfully',
            'data' => $notification
        ]);
    }
}
