<?php

namespace App\Http\Controllers\API\Backend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ColorSetRequest;
use App\Http\Resources\Backend\ColorSetResource;
use App\Models\Color;
use App\Models\ColorSet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ColorController extends Controller
{
    /**
     * Display a listing of color sets.
     */
    public function index(Request $request): JsonResponse
    {
        $colorSets = ColorSet::with('colors')->latest()->get();

        return ApiResponse::success(ColorSetResource::collection($colorSets));
    }

    /**
     * Store a newly created color set with child colors.
     */
    public function store(ColorSetRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $colorSet = ColorSet::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);

            if (is_array($request->color_code)) {
                foreach ($request->color_code as $key => $code) {
                    if (! empty($code)) {
                        Color::create([
                            'color_set_id' => $colorSet->id,
                            'color_code'   => $code,
                            'color_name'   => $request->color_name[$key] ?? null,
                        ]);
                    }
                }
            }

            return ApiResponse::created(new ColorSetResource($colorSet->load('colors')), 'Color set created successfully');
        });
    }

    /**
     * Display the specified color set.
     */
    public function show(string $id): JsonResponse
    {
        $colorSet = ColorSet::with('colors')->find((int) $id);

        if (! $colorSet) {
            return ApiResponse::notFound('Color set not found');
        }

        return ApiResponse::success(new ColorSetResource($colorSet));
    }

    /**
     * Update the specified color set.
     */
    public function update(ColorSetRequest $request, string $id): JsonResponse
    {
        $colorSet = ColorSet::find((int) $id);

        if (! $colorSet) {
            return ApiResponse::notFound('Color set not found');
        }

        return DB::transaction(function () use ($request, $colorSet) {
            $colorSet->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);

            Color::where('color_set_id', $colorSet->id)->delete();

            if (is_array($request->color_code)) {
                foreach ($request->color_code as $key => $code) {
                    if (! empty($code)) {
                        Color::create([
                            'color_set_id' => $colorSet->id,
                            'color_code'   => $code,
                            'color_name'   => $request->color_name[$key] ?? null,
                        ]);
                    }
                }
            }

            return ApiResponse::success(new ColorSetResource($colorSet->load('colors')), 'Color set updated successfully');
        });
    }

    /**
     * Remove the specified color set.
     */
    public function destroy(string $id): JsonResponse
    {
        $colorSet = ColorSet::find((int) $id);

        if (! $colorSet) {
            return ApiResponse::notFound('Color set not found');
        }

        $colorSet->delete();
        Color::where('color_set_id', (int) $id)->delete();

        return ApiResponse::success(null, 'Color set deleted successfully');
    }
}
