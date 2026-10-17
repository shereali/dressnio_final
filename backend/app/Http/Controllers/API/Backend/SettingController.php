<?php

namespace App\Http\Controllers\API\Backend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\PaymentMethods;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Get all settings (Admin). Credentials are masked: only whether they are set is shown.
     */
    public function index(): JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'key');

        foreach (Setting::SECRET_KEYS as $key) {
            $plain = (string) Setting::read($key, '');
            $settings[$key] = $plain === '' ? '' : str_repeat('•', 8) . substr($plain, -4);
        }

        return ApiResponse::success($settings);
    }

    /**
     * Update settings in bulk (Admin).
     */
    public function update(Request $request): JsonResponse
    {
        $data = $request->except(Setting::SECRET_KEYS);

        foreach ($data as $key => $value) {
            Setting::write($key, $value);
        }

        // A credential is only replaced when a new value is typed in; the masked value means "keep".
        foreach (Setting::SECRET_KEYS as $key) {
            $value = $request->input($key);

            if (is_string($value) && $value !== '' && ! str_contains($value, '•')) {
                Setting::write($key, trim($value));
            }
        }

        Cache::forget('dressnio_public_settings');

        return ApiResponse::success($data, 'Settings updated successfully');
    }

    /**
     * Send a test SMS with the saved gateway settings (Admin).
     */
    public function smsTest(Request $request, SmsService $sms): JsonResponse
    {
        $request->validate([
            'number' => ['required', 'string', 'regex:/^01[3-9]\d{8}$/'],
        ], ['number.regex' => 'Enter a valid Bangladeshi mobile number, like 01712345678.']);

        if (! $sms->isConfigured()) {
            return ApiResponse::error('Turn on SMS and save the API key and caller ID first.', 422);
        }

        $result = $sms->send($request->number, 'Dressnio test message. Your SMS settings work.');

        return $result['ok']
            ? ApiResponse::success(null, 'Test SMS sent')
            : ApiResponse::error($result['error'] ?? 'The test SMS failed.', 502);
    }

    /**
     * Public settings endpoint for Storefront.
     */
    public function publicSettings(): JsonResponse
    {
        $settings = Cache::remember('dressnio_public_settings', 3600, function () {
            $keys = [
                'announcement',
                'contact_phone',
                'contact_email',
                'contact_address',
                'facebook_url',
                'instagram_url',
                'shipping_fee',
                'shipping_fee_outside',
                'free_shipping_above',
                'bkash_number',
                'nagad_number',
                'about_text',
            ];

            return Setting::whereIn('key', $keys)->pluck('value', 'key');
        });

        // Payment methods change often and carry numbers customers pay to, so they skip the cache
        $payload = $settings->toArray();
        $payload['payment_methods'] = PaymentMethods::enabled();

        return ApiResponse::success($payload);
    }
}
