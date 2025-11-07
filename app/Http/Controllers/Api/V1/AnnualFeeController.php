<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\AnnualFee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AnnualFeeResource;
use Illuminate\Support\Facades\Validator;

class AnnualFeeController extends Controller
{
    /**
     * Display a listing of annual fees
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $coopId = $request->input('coop_id', '');
        $year = $request->input('year', '');

        $annualFees = AnnualFee::query()
            ->when($coopId, function ($query) use ($coopId) {
                $query->where('coopId', $coopId);
            })
            ->when($year, function ($query) use ($year) {
                $query->where('annual_year', $year);
            })
            ->orderBy('coopId', 'asc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Annual fees retrieved successfully',
            'data' => AnnualFeeResource::collection($annualFees),
            'meta' => [
                'current_page' => $annualFees->currentPage(),
                'per_page' => $annualFees->perPage(),
                'total' => $annualFees->total(),
                'last_page' => $annualFees->lastPage()
            ]
        ]);
    }

    /**
     * Store a newly created annual fee
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coopId' => 'required|exists:members,coopId',
            'annual_savings' => 'required|numeric|min:0',
            'annual_fee' => 'required|numeric|min:0',
            'annual_year' => 'required|integer|min:1900|max:2100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['userId'] = auth('api')->id();
        $data['total_savings'] = $data['annual_savings'] + $data['annual_fee'];
        $data['status'] = 1;

        $annualFee = AnnualFee::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Annual fee created successfully',
            'data' => new AnnualFeeResource($annualFee)
        ], 201);
    }

    /**
     * Display the specified annual fee
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $annualFee = AnnualFee::find($id);

        if (!$annualFee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Annual fee not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Annual fee retrieved successfully',
            'data' => new AnnualFeeResource($annualFee)
        ]);
    }

    /**
     * Update the specified annual fee
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $annualFee = AnnualFee::find($id);

        if (!$annualFee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Annual fee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'annual_savings' => 'sometimes|required|numeric|min:0',
            'annual_fee' => 'sometimes|required|numeric|min:0',
            'annual_year' => 'sometimes|required|integer|min:1900|max:2100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        
        $annualSavings = $data['annual_savings'] ?? $annualFee->annual_savings;
        $annualFeeAmount = $data['annual_fee'] ?? $annualFee->annual_fee;
        $data['total_savings'] = $annualSavings + $annualFeeAmount;

        $annualFee->updateEditDates();
        $annualFee->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Annual fee updated successfully',
            'data' => new AnnualFeeResource($annualFee)
        ]);
    }

    /**
     * Remove the specified annual fee
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $annualFee = AnnualFee::find($id);

        if (!$annualFee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Annual fee not found'
            ], 404);
        }

        $annualFee->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Annual fee deleted successfully'
        ], 200);
    }
}
