<?php

namespace App\Http\Controllers\API\Backend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Admin Login via Sanctum.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return ApiResponse::unauthorized('Invalid admin credentials');
        }

        // Revoke existing tokens for a clean session
        $user->tokens()->delete();

        $token = $user->createToken('admin-token', ['admin'])->plainTextToken;

        return ApiResponse::success([
            'token' => $token,
            'user'  => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role ?? 'admin',
            ],
        ], 'Login successful');
    }

    /**
     * Create a new sub-admin/staff user.
     */
    public function createUser(Request $request): JsonResponse
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'mobile_no' => 'nullable|string|unique:users,mobile_no',
            'role'      => 'nullable|string|in:admin,editor,manager',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'mobile_no' => $request->mobile_no,
            'password'  => Hash::make($request->password),
            'role'      => $request->role ?? 'admin',
        ]);

        return ApiResponse::created([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ], 'User created successfully');
    }

    /**
     * Admin Logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logged out successfully');
    }
}
