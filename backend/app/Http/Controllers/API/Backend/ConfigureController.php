<?php

namespace App\Http\Controllers\API\Backend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfigureController extends Controller
{
    public function facebook(Request $request): JsonResponse
    {
        $request->validate([
            'facebook_url' => 'nullable|url',
            'pixel_id'     => 'nullable|string',
        ]);

        Setting::updateOrCreate(['key' => 'facebook_url'], ['value' => $request->facebook_url]);
        Setting::updateOrCreate(['key' => 'facebook_pixel_id'], ['value' => $request->pixel_id]);

        return ApiResponse::success(null, 'Facebook configuration saved');
    }

    public function instagram(Request $request): JsonResponse
    {
        $request->validate([
            'instagram_url' => 'nullable|url',
        ]);

        Setting::updateOrCreate(['key' => 'instagram_url'], ['value' => $request->instagram_url]);

        return ApiResponse::success(null, 'Instagram configuration saved');
    }
}
