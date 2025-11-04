<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\MemberResource;
use App\Http\Resources\Api\MemberCollection;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    /**
     * Display a listing of members
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search', '');
        $groupFilter = $request->input('group_id', '');

        $members = Member::query()
            ->when($search, function ($query) use ($search) {
                $query->where('surname', 'like', "%{$search}%")
                    ->orWhere('otherNames', 'like', "%{$search}%")
                    ->orWhere('coopId', 'like', "%{$search}%");
            })
            ->when($groupFilter, function ($query) use ($groupFilter) {
                $query->where('groupId', $groupFilter);
            })
            ->orderBy('coopId', 'asc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Members retrieved successfully',
            'data' => new MemberCollection($members)
        ]);
    }

    /**
     * Store a newly created member
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coopId' => 'required|unique:members,coopId',
            'surname' => 'required|string|max:255',
            'otherNames' => 'required|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:10',
            'religion' => 'nullable|string|max:255',
            'phoneNumber' => 'nullable|string|max:20',
            'bankName' => 'nullable|string|max:255',
            'accountNumber' => 'nullable|string|max:50',
            'nextOfKinName' => 'nullable|string|max:255',
            'nextOfKinPhoneNumber' => 'nullable|string|max:20',
            'yearJoined' => 'nullable|integer|min:1900|max:2100',
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
        
        $member = Member::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Member created successfully',
            'data' => new MemberResource($member)
        ], 201);
    }

    /**
     * Display the specified member
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json([
                'status' => 'error',
                'message' => 'Member not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Member retrieved successfully',
            'data' => new MemberResource($member)
        ]);
    }

    /**
     * Update the specified member
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json([
                'status' => 'error',
                'message' => 'Member not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'coopId' => 'sometimes|required|unique:members,coopId,' . $id,
            'surname' => 'sometimes|required|string|max:255',
            'otherNames' => 'sometimes|required|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:10',
            'religion' => 'nullable|string|max:255',
            'phoneNumber' => 'nullable|string|max:20',
            'bankName' => 'nullable|string|max:255',
            'accountNumber' => 'nullable|string|max:50',
            'nextOfKinName' => 'nullable|string|max:255',
            'nextOfKinPhoneNumber' => 'nullable|string|max:20',
            'yearJoined' => 'nullable|integer|min:1900|max:2100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $member->updateEditDates();
        $member->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Member updated successfully',
            'data' => new MemberResource($member)
        ]);
    }

    /**
     * Remove the specified member
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $member = Member::find($id);

        if (!$member) {
            return response()->json([
                'status' => 'error',
                'message' => 'Member not found'
            ], 404);
        }

        $member->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Member deleted successfully'
        ], 200);
    }

    /**
     * Get member by coopId
     *
     * @param string $coopId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByCoopId($coopId)
    {
        $member = Member::where('coopId', $coopId)->first();

        if (!$member) {
            return response()->json([
                'status' => 'error',
                'message' => 'Member not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Member retrieved successfully',
            'data' => new MemberResource($member)
        ]);
    }
}
