<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\LoanCapture;
use App\Models\ActiveLoans;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\LoanResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * Display a listing of loans
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $search = $request->input('search', '');
        $status = $request->input('status', null);

        $loans = LoanCapture::query()
            ->when($search, function ($query) use ($search) {
                $query->where('coopId', 'like', '%' . $search . '%');
            })
            ->when($status !== null, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('coopId', 'asc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Loans retrieved successfully',
            'data' => LoanResource::collection($loans),
            'meta' => [
                'current_page' => $loans->currentPage(),
                'per_page' => $loans->perPage(),
                'total' => $loans->total(),
                'last_page' => $loans->lastPage()
            ]
        ]);
    }

    /**
     * Store a newly created loan
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coopId' => 'required|exists:members,coopId',
            'loanAmount' => 'required|numeric|min:0',
            'loanDate' => 'required|date',
            'guarantor1' => 'nullable|exists:members,coopId',
            'guarantor2' => 'nullable|exists:members,coopId',
            'guarantor3' => 'nullable|exists:members,coopId',
            'guarantor4' => 'nullable|exists:members,coopId',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if member already has an active loan
        $activeLoan = ActiveLoans::where('coopId', $request->coopId)->first();
        if ($activeLoan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Member already has an active loan'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
            $data['userId'] = auth('api')->id();
            $data['status'] = 1;
            $data['repaymentDate'] = date('Y-m-d', strtotime($request->loanDate . ' + 540 days'));

            $loan = LoanCapture::create($data);
            
            // Add to active loans
            $loan->addToActiveLoanWithDate();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Loan created successfully',
                'data' => new LoanResource($loan)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating loan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified loan
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $loan = LoanCapture::find($id);

        if (!$loan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loan not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Loan retrieved successfully',
            'data' => new LoanResource($loan)
        ]);
    }

    /**
     * Update the specified loan
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $loan = LoanCapture::find($id);

        if (!$loan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loan not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'loanAmount' => 'sometimes|required|numeric|min:0',
            'loanDate' => 'sometimes|required|date',
            'guarantor1' => 'nullable|exists:members,coopId',
            'guarantor2' => 'nullable|exists:members,coopId',
            'guarantor3' => 'nullable|exists:members,coopId',
            'guarantor4' => 'nullable|exists:members,coopId',
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
            $prevAmount = $loan->loanAmount;
            
            $loan->update($validator->validated());
            
            if ($request->has('loanDate')) {
                $loan->repaymentDate = date('Y-m-d', strtotime($loan->loanDate . ' + 540 days'));
            }
            
            $loan->updateEditDates($prevAmount);
            $loan->updateActiveLoanAmount();
            $loan->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Loan updated successfully',
                'data' => new LoanResource($loan)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating loan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified loan
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $loan = LoanCapture::find($id);

        if (!$loan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loan not found'
            ], 404);
        }

        DB::beginTransaction();
        try {
            ActiveLoans::where('coopId', $loan->coopId)->first()?->delete();
            $loan->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Loan deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting loan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark loan as completed
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete($id)
    {
        $loan = LoanCapture::find($id);

        if (!$loan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loan not found'
            ], 404);
        }

        try {
            $loan->completedLoan();

            return response()->json([
                'status' => 'success',
                'message' => 'Loan marked as completed successfully',
                'data' => new LoanResource($loan)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error completing loan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active loans
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeLoans(Request $request)
    {
        $perPage = $request->input('per_page', 25);

        $activeLoans = ActiveLoans::with('member')
            ->orderBy('coopId', 'asc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Active loans retrieved successfully',
            'data' => $activeLoans
        ]);
    }
}
