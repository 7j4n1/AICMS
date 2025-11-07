<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ItemCapture;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ItemCaptureResource;
use Illuminate\Support\Facades\Validator;

class ItemCaptureController extends Controller
{
    /**
     * Display a listing of items
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 25);
        $coopId = $request->input('coop_id', '');
        $categoryId = $request->input('category_id', '');
        $paymentStatus = $request->input('payment_status', '');

        $items = ItemCapture::query()
            ->with(['category', 'member'])
            ->when($coopId, function ($query) use ($coopId) {
                $query->where('coopId', $coopId);
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($paymentStatus !== '', function ($query) use ($paymentStatus) {
                $query->where('payment_status', $paymentStatus);
            })
            ->orderBy('buyingDate', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Items retrieved successfully',
            'data' => ItemCaptureResource::collection($items),
            'meta' => [
                'current_page' => $items->currentPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'last_page' => $items->lastPage()
            ]
        ]);
    }

    /**
     * Store a newly created item
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coopId' => 'required|exists:members,coopId',
            'category_id' => 'required|exists:item_categories,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'buyingDate' => 'required|date',
            'payment_timeframe' => 'required|integer|min:1',
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
        $data['payment_status'] = 0; // Not paid
        $data['loanPaid'] = 0;
        $data['loanBalance'] = $data['price'] * $data['quantity'];
        $data['repaymentDate'] = date('Y-m-d', strtotime($data['buyingDate'] . ' + ' . $data['payment_timeframe'] . ' days'));

        $item = ItemCapture::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Item created successfully',
            'data' => new ItemCaptureResource($item->load(['category', 'member']))
        ], 201);
    }

    /**
     * Display the specified item
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = ItemCapture::with(['category', 'member', 'repayments'])->find($id);

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Item retrieved successfully',
            'data' => new ItemCaptureResource($item)
        ]);
    }

    /**
     * Update the specified item
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $item = ItemCapture::find($id);

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|required|exists:item_categories,id',
            'quantity' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|numeric|min:0',
            'description' => 'nullable|string',
            'buyingDate' => 'sometimes|required|date',
            'payment_timeframe' => 'sometimes|required|integer|min:1',
            'payment_status' => 'sometimes|required|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        
        // Recalculate balance if price or quantity changed
        if (isset($data['price']) || isset($data['quantity'])) {
            $price = $data['price'] ?? $item->price;
            $quantity = $data['quantity'] ?? $item->quantity;
            $data['loanBalance'] = ($price * $quantity) - $item->loanPaid;
        }

        // Recalculate repayment date if needed
        if (isset($data['buyingDate']) || isset($data['payment_timeframe'])) {
            $buyingDate = $data['buyingDate'] ?? $item->buyingDate;
            $timeframe = $data['payment_timeframe'] ?? $item->payment_timeframe;
            $data['repaymentDate'] = date('Y-m-d', strtotime($buyingDate . ' + ' . $timeframe . ' days'));
        }

        $item->updateEditDates();
        $item->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Item updated successfully',
            'data' => new ItemCaptureResource($item->load(['category', 'member']))
        ]);
    }

    /**
     * Remove the specified item
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $item = ItemCapture::find($id);

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item not found'
            ], 404);
        }

        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item deleted successfully'
        ], 200);
    }
}
