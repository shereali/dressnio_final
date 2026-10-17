<?php

namespace App\Http\Controllers\API\Frontend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LoginCode;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private const OTP_MINUTES = 5;
    private const RESEND_SECONDS = 60;
    private const MAX_ATTEMPTS = 5;

    /** Accepts 01XXXXXXXXX, 8801XXXXXXXXX and +8801XXXXXXXXX and returns the local 01XXXXXXXXX form. */
    private function normalizeMobile(?string $value): string
    {
        $digits = preg_replace('/\D/', '', (string) $value);

        if (str_starts_with($digits, '880')) {
            $digits = '0' . substr($digits, 3);
        }

        return $digits;
    }

    private function hashCode(string $mobile, string $otp): string
    {
        return hash_hmac('sha256', $mobile . '|' . $otp, (string) config('app.key'));
    }

    /**
     * Send a one-time code to a mobile number (SMS through the gateway set in admin settings).
     */
    public function sendOtp(Request $request, SmsService $sms): JsonResponse
    {
        $request->merge(['mobile_no' => $this->normalizeMobile($request->input('mobile_no'))]);

        $request->validate([
            'mobile_no' => ['required', 'string', 'regex:/^01[3-9]\d{8}$/'],
        ], [
            'mobile_no.regex' => 'Enter a valid Bangladeshi mobile number, like 01712345678.',
        ]);

        $mobile = $request->mobile_no;
        $existing = LoginCode::where('mobile_no', $mobile)->first();

        if ($existing?->last_sent_at && $existing->last_sent_at->gt(now()->subSeconds(self::RESEND_SECONDS))) {
            $wait = self::RESEND_SECONDS - $existing->last_sent_at->diffInSeconds(now());

            return ApiResponse::error(
                "Please wait {$wait} seconds before asking for another code.",
                429,
                ['retry_after' => max(1, (int) $wait)]
            );
        }

        $otp = (string) random_int(100000, 999999);
        $viaSms = $sms->isConfigured();

        // Without a configured gateway the code is only available in local development
        if (! $viaSms && ! app()->environment('local')) {
            return ApiResponse::error('Sign in by SMS is not available right now. Please try again later.', 503);
        }

        if ($viaSms) {
            $result = $sms->send($mobile, $sms->otpMessage($otp, self::OTP_MINUTES));

            if (! $result['ok']) {
                return ApiResponse::error('We could not send the SMS. Check the number and try again.', 502);
            }
        } else {
            Log::info("OTP for {$mobile}: {$otp} (SMS gateway not configured)");
        }

        LoginCode::updateOrCreate(
            ['mobile_no' => $mobile],
            [
                'code'         => $this->hashCode($mobile, $otp),
                'attempts'     => 0,
                'last_sent_at' => now(),
                'expires_at'   => now()->addMinutes(self::OTP_MINUTES),
                'used_at'      => null,
            ]
        );

        return ApiResponse::success([
            'mobile_no'  => $mobile,
            'expires_in' => self::OTP_MINUTES * 60,
            'resend_in'  => self::RESEND_SECONDS,
            'length'     => 6,
            // Only exposed in local development when no SMS gateway is configured
            'dev_otp'    => (! $viaSms && app()->environment('local')) ? $otp : null,
        ], 'Code sent');
    }

    /**
     * Verify the code and sign the customer in, creating the account on first use.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->merge(['mobile_no' => $this->normalizeMobile($request->input('mobile_no'))]);

        $request->validate([
            'mobile_no' => ['required', 'string', 'regex:/^01[3-9]\d{8}$/'],
            'otp'       => ['required', 'string', 'regex:/^\d{6}$/'],
        ], [
            'otp.regex' => 'Enter the 6-digit code from your SMS.',
        ]);

        $mobile = $request->mobile_no;
        $code = LoginCode::where('mobile_no', $mobile)->whereNull('used_at')->first();

        if (! $code || $code->expires_at->isPast()) {
            return ApiResponse::error('This code has expired. Ask for a new one.', 422, ['expired' => true]);
        }

        if ($code->attempts >= self::MAX_ATTEMPTS) {
            return ApiResponse::error('Too many wrong codes. Ask for a new one.', 429, ['expired' => true]);
        }

        if (! hash_equals($code->code, $this->hashCode($mobile, $request->otp))) {
            $code->increment('attempts');
            $left = max(0, self::MAX_ATTEMPTS - $code->attempts);

            return ApiResponse::error(
                $left > 0 ? "That code is not right. You have {$left} " . ($left === 1 ? 'try' : 'tries') . ' left.' : 'Too many wrong codes. Ask for a new one.',
                422,
                ['attempts_left' => $left]
            );
        }

        $code->update(['used_at' => now()]);

        $customer = Customer::firstOrCreate(
            ['mobile_no' => $mobile],
            [
                'name'      => 'Customer ' . substr($mobile, -4),
                'is_active' => true,
            ]
        );

        if (! $customer->is_active) {
            return ApiResponse::error('This account is disabled. Contact support.', 403);
        }

        $token = $customer->createToken('customer-token', ['customer'])->plainTextToken;

        return ApiResponse::success([
            'token'    => $token,
            'customer' => $customer,
            'is_new'   => $customer->wasRecentlyCreated,
        ], 'Signed in');
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
