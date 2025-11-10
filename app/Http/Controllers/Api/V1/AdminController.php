<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AdminResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    /**
     * Display a listing of admins
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $search = $request->input('search', '');

        $admins = Admin::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Admins retrieved successfully',
            'data' => AdminResource::collection($admins),
            'meta' => [
                'current_page' => $admins->currentPage(),
                'per_page' => $admins->perPage(),
                'total' => $admins->total(),
                'last_page' => $admins->lastPage()
            ]
        ]);
    }

    /**
     * Store a newly created admin
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins',
            'email' => 'nullable|string|email|max:255|unique:admins',
            'password' => 'required|string|min:6',
            'coopId' => 'nullable|exists:members,coopId',
            'role' => 'required|string|in:admin,member,super-admin,manager'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['password'] = Hash::make($data['password']);
        $data['userId'] = auth('api')->id();

        $admin = Admin::create($data);
        if(!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create admin'
            ], 500);
        }

        $admin->assignRole($data['role']);

        $roleExists = Role::findByName($data['role'], 'api');
        if ($roleExists) {
            $admin->roles()->attach($roleExists->id);
        }


        return response()->json([
            'status' => 'success',
            'message' => 'Admin created successfully',
            'data' => new AdminResource($admin)
        ], 201);
    }

    /**
     * Display the specified admin
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Admin retrieved successfully',
            'data' => new AdminResource($admin)
        ]);
    }

    /**
     * Update the specified admin
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255|unique:admins,username,' . $id,
            'email' => 'nullable|string|email|max:255|unique:admins,email,' . $id,
            'password' => 'sometimes|required|string|min:6',
            'coopId' => 'nullable|exists:members,coopId',
            'role' => 'sometimes|required|string|in:admin,super-admin,manager,member'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $admin->update($data);

        if (isset($data['role'])) {
            $admin->syncRoles([$data['role']]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Admin updated successfully',
            'data' => new AdminResource($admin)
        ]);
    }

    /**
     * Remove the specified admin
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin not found'
            ], 404);
        }

        $admin->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Admin deleted successfully'
        ], 200);
    }
}
