<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LoanEligibilitySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LoanEligibilityController extends Controller
{
    /**
     * Display a listing of loan eligibility settings
     */
    public function index()
    {
        $settings = LoanEligibilitySetting::all();

        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }

    /**
     * Get the active loan eligibility formula
     */
    public function getActive()
    {
        $setting = LoanEligibilitySetting::active()->first();

        if (!$setting) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active loan eligibility formula found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $setting
        ]);
    }

    /**
     * Store a newly created loan eligibility setting
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'formula_key' => 'required|string|unique:loan_eligibility_settings,formula_key',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'formula' => 'required|string',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // If setting active to true, deactivate all others
        if ($request->active) {
            LoanEligibilitySetting::where('active', true)->update(['active' => false]);
        }

        $setting = LoanEligibilitySetting::create($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Loan eligibility setting created successfully',
            'data' => $setting
        ], 201);
    }

    /**
     * Display the specified loan eligibility setting
     */
    public function show(string $id)
    {
        $setting = LoanEligibilitySetting::find($id);

        if (!$setting) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loan eligibility setting not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $setting
        ]);
    }

    /**
     * Update the specified loan eligibility setting
     */
    public function update(Request $request, string $id)
    {
        $setting = LoanEligibilitySetting::find($id);

        if (!$setting) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loan eligibility setting not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'formula_key' => 'sometimes|string|unique:loan_eligibility_settings,formula_key,' . $id,
            'name' => 'sometimes|string',
            'description' => 'nullable|string',
            'formula' => 'sometimes|string',
            'active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // If setting active to true, deactivate all others
        if ($request->has('active') && $request->active) {
            LoanEligibilitySetting::where('id', '!=', $id)
                ->where('active', true)
                ->update(['active' => false]);
        }

        $setting->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Loan eligibility setting updated successfully',
            'data' => $setting
        ]);
    }

    /**
     * Remove the specified loan eligibility setting
     */
    public function destroy(string $id)
    {
        $setting = LoanEligibilitySetting::find($id);

        if (!$setting) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loan eligibility setting not found'
            ], 404);
        }

        if ($setting->active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete active loan eligibility setting'
            ], 422);
        }

        $setting->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Loan eligibility setting deleted successfully'
        ]);
    }

    /**
     * Calculate loan eligibility for a member
     */
    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coopId' => 'required|exists:members,coopId',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $setting = LoanEligibilitySetting::active()->first();

        if (!$setting) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active loan eligibility formula configured'
            ], 404);
        }

        // Get member's total savings and shares
        $coopId = $request->coopId;
        
        $totals = DB::table('payment_captures')
            ->where('coopId', $coopId)
            ->selectRaw('SUM(savingAmount) as total_savings, SUM(shareAmount) as total_shares')
            ->first();

        $savings = $totals->total_savings ?? 0;
        $shares = $totals->total_shares ?? 0;

        // Calculate eligibility
        $eligibleAmount = $setting->calculateEligibility($savings, $shares);

        // Get active loan balance
        $activeLoan = DB::table('active_loans')
            ->where('coopId', $coopId)
            ->sum('loanBalance');

        // Available amount is eligible minus active loan
        $availableAmount = max(0, $eligibleAmount - $activeLoan);

        return response()->json([
            'status' => 'success',
            'data' => [
                'eligible_amount' => $eligibleAmount,
                'active_loan_balance' => $activeLoan,
                'available_amount' => $availableAmount,
                'total_savings' => $savings,
                'total_shares' => $shares,
                'formula_used' => $setting->formula,
            ]
        ]);
    }
}
