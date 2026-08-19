<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class Helpers
{
    /**
     * Build a public URL for an uploaded asset.
     * Accepts a full URL, a path already starting with "/", or a bare filename.
     */
    public static function publicPath(mixed $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return asset($path);
        }

        return asset('/storage/uploads/' . $path);
    }

    /**
     * Generate a unique human-friendly order number, e.g. DN-20260819-XXXXXX.
     */
    public static function orderNumber(): string
    {
        return 'DN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
