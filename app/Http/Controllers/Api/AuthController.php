<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{

    public function testOutput()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'You are authorized to access this route'
        ], 401);
    }
    // login function
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // validate the request
        $credential = $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        // Debug incoming request
        Log::info('Login attempt', [
            'username' => $request->username,
            'headers' => $request->headers->all(),
        ]);

        // check the credentials
        if (!$token = auth('api')->attempt($credential)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid login details'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'You have successfully logged in',
            'access_token' => $token,
            'data' => [
                'user' => auth('api')->user(),
                ''
            ]
        ]);
    }

    // my profile function
    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => auth('api')->user()
            ]
        ]);
    }

    // password reset function
    public function resetPassword(Request $request)
    {
        // validate the request
        $request->validate([
            'email' => 'required|email'
        ]);

        // check if the user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        // generate the token
        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'user' => $user
        ]);
    }

}
