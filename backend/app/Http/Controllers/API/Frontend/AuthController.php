<?php

namespace App\Http\Controllers\API\Frontend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LoginCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Send OTP for phone-based authentication.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'mobile_no' => 'required|string|regex:/^01[3-9]\d{8}$/',
        ]);

        $otp = (string) rand(1000, 9999);

        // Store OTP code in LoginCode model with 10-minute expiry
        LoginCode::updateOrCreate(
            ['mobile_no' => $request->mobile_no],
            [
                'code'       => $otp,
                'expires_at' => now()->addMinutes(10),
            ]
        );

        Log::info("OTP for {$request->mobile_no}: {$otp}");

        return ApiResponse::success([
            'mobile_no' => $request->mobile_no,
            'message'   => 'OTP sent successfully',
            // Return OTP in dev mode for smooth testing
            'dev_otp'   => app()->environment('local') ? $otp : null,
        ], 'OTP sent successfully');
    }

    /**
     * Verify OTP and log in / register customer.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'mobile_no' => 'required|string',
            'otp'       => 'required|string|size:4',
        ]);

        $loginCode = LoginCode::where('mobile_no', $request->mobile_no)
            ->where('code', $request->otp)
            ->where('expires_at', '>=', now())
            ->first();

        // Allow fallback demo code 1234 for testing in local environment
        if (! $loginCode && ! (app()->environment('local') && $request->otp === '1234')) {
            return ApiResponse::error('Invalid or expired OTP code', 422);
        }

        if ($loginCode) {
            $loginCode->delete();
        }

        $customer = Customer::firstOrCreate(
            ['mobile_no' => $request->mobile_no],
            [
                'name'      => 'Customer ' . substr($request->mobile_no, -4),
                'is_active' => true,
            ]
        );

        $token = $customer->createToken('customer-token', ['customer'])->plainTextToken;

        return ApiResponse::success([
            'token'    => $token,
            'customer' => $customer,
        ], 'Verification successful');
    }

    /**
     * Customer registration with email & password.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'mobile_no' => 'required|string|regex:/^01[3-9]\d{8}$/|unique:customers,mobile_no',
            'email'     => 'nullable|email|unique:customers,email',
            'password'  => 'required|string|min:6',
        ]);

        $customer = Customer::create([
            'name'      => $request->name,
            'mobile_no' => $request->mobile_no,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'is_active' => true,
        ]);

        $token = $customer->createToken('customer-token', ['customer'])->plainTextToken;

        return ApiResponse::created([
            'token'    => $token,
            'customer' => $customer,
        ], 'Account created successfully');
    }

    /**
     * Customer password login.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'mobile_no' => 'required|string',
            'password'  => 'required|string',
        ]);

        $customer = Customer::where('mobile_no', $request->mobile_no)
            ->orWhere('email', $request->mobile_no)
            ->first();

        if (! $customer || ! Hash::check($request->password, $customer->password)) {
            return ApiResponse::unauthorized('Invalid mobile number or password');
        }

        $token = $customer->createToken('customer-token', ['customer'])->plainTextToken;

        return ApiResponse::success([
            'token'    => $token,
            'customer' => $customer,
        ], 'Login successful');
    }

    /**
     * Get authenticated customer profile.
     */
    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success($request->user());
    }

    /**
     * Update customer profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $customer = $request->user();

        $request->validate([
            'name'    => 'nullable|string|max:255',
            'email'   => 'nullable|email|unique:customers,email,' . $customer->id,
            'address' => 'nullable|string',
            'city'    => 'nullable|string',
        ]);

        $customer->update($request->only(['name', 'email', 'address', 'city']));

        return ApiResponse::success($customer, 'Profile updated successfully');
    }

    /**
     * Customer Logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logged out successfully');
    }
}
