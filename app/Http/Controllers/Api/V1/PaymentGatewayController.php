<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of payment gateways
     */
    public function index()
    {
        $gateways = PaymentGateway::all();

        return response()->json([
            'status' => 'success',
            'data' => $gateways
        ]);
    }

    /**
     * Store a newly created payment gateway
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:payment_gateways,name',
            'enabled' => 'boolean',
            'public_key' => 'nullable|string',
            'secret_key' => 'nullable|string',
            'merchant_email' => 'nullable|email',
            'additional_config' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $gateway = PaymentGateway::create($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Payment gateway created successfully',
            'data' => $gateway
        ], 201);
    }

    /**
     * Display the specified payment gateway
     */
    public function show(string $id)
    {
        $gateway = PaymentGateway::find($id);

        if (!$gateway) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment gateway not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $gateway
        ]);
    }

    /**
     * Update the specified payment gateway
     */
    public function update(Request $request, string $id)
    {
        $gateway = PaymentGateway::find($id);

        if (!$gateway) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment gateway not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|unique:payment_gateways,name,' . $id,
            'enabled' => 'sometimes|boolean',
            'public_key' => 'nullable|string',
            'secret_key' => 'nullable|string',
            'merchant_email' => 'nullable|email',
            'additional_config' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $gateway->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Payment gateway updated successfully',
            'data' => $gateway
        ]);
    }

    /**
     * Remove the specified payment gateway
     */
    public function destroy(string $id)
    {
        $gateway = PaymentGateway::find($id);

        if (!$gateway) {
            return response()->json([
                'status' => 'error',
                'message' => 'Payment gateway not found'
            ], 404);
        }

        $gateway->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Payment gateway deleted successfully'
        ]);
    }
}
