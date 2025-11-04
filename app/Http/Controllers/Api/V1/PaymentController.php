<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\PaymentCapture;
use App\Models\ActiveLoans;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PaymentResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $coopId = $request->input('coop_id', '');
        $startDate = $request->input('start_date', '');
        $endDate = $request->input('end_date', '');

        $payments = PaymentCapture::query()
            ->when($coopId, function ($query) use ($coopId) {
                $query->where('coopId', $coopId);
            })
            ->when($startDate, function ($query) use ($startDate) {
                $query->whereDate('paymentDate', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->whereDate('paymentDate', '<=', $endDate);
            })
            ->orderBy('paymentDate', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Payments retrieved successfully',
            'data' => PaymentResource::collection($payments),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
                'last_page' => $payments->lastPage()
            ]
        ]);
    }

    /**
     * Store a newly created payment
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coopId' => 'required|exists:members,coopId',
            'loanAmount' => 'nullable|numeric|min:0',
            'savingAmount' => 'nullable|numeric|min:0',
            'shareAmount' => 'nullable|numeric|min:0',
            'others' => 'nullable|numeric|min:0',
            'adminCharge' => 'nullable|numeric|min:0',
            'paymentDate' => 'required|date',
            'splitOption' => 'nullable|string|max:50',
            'otherSavingsType' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
            $data['userId'] = auth('api')->id();
            
            // Calculate total amount
            $loanAmount = $data['loanAmount'] ?? 0;
            $savingAmount = $data['savingAmount'] ?? 0;
            $shareAmount = $data['shareAmount'] ?? 0;
            $others = $data['others'] ?? 0;
            $adminCharge = $data['adminCharge'] ?? 0;
            
            $data['totalAmount'] = $loanAmount + $savingAmount + $shareAmount + $others + $adminCharge;

            $payment = PaymentCapture::create($data);

            // Update active loan if loan amount is paid
            if ($loanAmount > 0) {
                $activeLoan = ActiveLoans::where('coopId', $data['coopId'])->first();
                if ($activeLoan) {
                    $activeLoan->setPayment($loanAmount, $data['paymentDate']);
                } else {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'No active loan found for this member'
                    ], 422);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment created successfully',
                'data' => new PaymentResource($payment)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified payment
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $payment = PaymentCapture::find($id);

        if (!$payment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Payment retrieved successfully',
            'data' => new PaymentResource($payment)
        ]);
    }

    /**
     * Update the specified payment
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $payment = PaymentCapture::find($id);

        if (!$payment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'loanAmount' => 'sometimes|nullable|numeric|min:0',
            'savingAmount' => 'sometimes|nullable|numeric|min:0',
            'shareAmount' => 'sometimes|nullable|numeric|min:0',
            'others' => 'sometimes|nullable|numeric|min:0',
            'adminCharge' => 'sometimes|nullable|numeric|min:0',
            'paymentDate' => 'sometimes|required|date',
            'splitOption' => 'nullable|string|max:50',
            'otherSavingsType' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $prevLoanAmount = $payment->loanAmount;
            
            $data = $validator->validated();
            
            // Recalculate total amount
            $loanAmount = $data['loanAmount'] ?? $payment->loanAmount;
            $savingAmount = $data['savingAmount'] ?? $payment->savingAmount;
            $shareAmount = $data['shareAmount'] ?? $payment->shareAmount;
            $others = $data['others'] ?? $payment->others;
            $adminCharge = $data['adminCharge'] ?? $payment->adminCharge;
            
            $data['totalAmount'] = $loanAmount + $savingAmount + $shareAmount + $others + $adminCharge;

            // Update loan if loan amount changed
            if (isset($data['loanAmount']) && $data['loanAmount'] != $prevLoanAmount) {
                $payment->updateLoan($prevLoanAmount, $data['loanAmount']);
            }

            $payment->updateEditDates();
            $payment->update($data);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment updated successfully',
                'data' => new PaymentResource($payment)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified payment
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $payment = PaymentCapture::find($id);

        if (!$payment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment not found'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // If there was a loan payment, revert it
            if ($payment->loanAmount > 0) {
                $activeLoan = ActiveLoans::where('coopId', $payment->coopId)->first();
                if ($activeLoan) {
                    $activeLoan->loanPaid -= $payment->loanAmount;
                    $activeLoan->loanBalance = $activeLoan->loanAmount - $activeLoan->loanPaid;
                    $activeLoan->save();
                }
            }

            $payment->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Payment deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting payment: ' . $e->getMessage()
            ], 500);
        }
    }
}
