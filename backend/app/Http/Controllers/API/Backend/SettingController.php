<?php

namespace App\Http\Controllers\API\Backend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Get all settings (Admin).
     */
    public function index(): JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'key');
        return ApiResponse::success($settings);
    }

    /**
     * Update settings in bulk (Admin).
     */
    public function update(Request $request): JsonResponse
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : (string) $value]
            );
        }

        Cache::forget('dressnio_public_settings');

        return ApiResponse::success($data, 'Settings updated successfully');
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

        return ApiResponse::success($settings);
    }
}
