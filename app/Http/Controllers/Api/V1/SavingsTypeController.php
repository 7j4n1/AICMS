<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SavingsType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SavingsTypeController extends Controller
{
    /**
     * Display a listing of savings types
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $active = $request->input('active');

        $query = SavingsType::query();

        if ($active !== null) {
            $query->where('active', (bool) $active);
        }

        $savingsTypes = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $savingsTypes->items(),
            'meta' => [
                'current_page' => $savingsTypes->currentPage(),
                'per_page' => $savingsTypes->perPage(),
                'total' => $savingsTypes->total(),
                'last_page' => $savingsTypes->lastPage()
            ]
        ]);
    }

    /**
     * Store a newly created savings type
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:savings_types,name',
            'description' => 'nullable|string',
            'active' => 'boolean',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $savingsType = SavingsType::create($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Savings type created successfully',
            'data' => $savingsType
        ], 201);
    }

    /**
     * Display the specified savings type
     */
    public function show(string $id)
    {
        $savingsType = SavingsType::find($id);

        if (!$savingsType) {
            return response()->json([
                'status' => 'error',
                'message' => 'Savings type not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $savingsType
        ]);
    }

    /**
     * Update the specified savings type
     */
    public function update(Request $request, string $id)
    {
        $savingsType = SavingsType::find($id);

        if (!$savingsType) {
            return response()->json([
                'status' => 'error',
                'message' => 'Savings type not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|unique:savings_types,name,' . $id,
            'description' => 'nullable|string',
            'active' => 'sometimes|boolean',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $savingsType->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Savings type updated successfully',
            'data' => $savingsType
        ]);
    }

    /**
     * Remove the specified savings type
     */
    public function destroy(string $id)
    {
        $savingsType = SavingsType::find($id);

        if (!$savingsType) {
            return response()->json([
                'status' => 'error',
                'message' => 'Savings type not found'
            ], 404);
        }

        // Check if any payments use this savings type
        if ($savingsType->payments()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete savings type with existing payments'
            ], 422);
        }

        $savingsType->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Savings type deleted successfully'
        ]);
    }
}
