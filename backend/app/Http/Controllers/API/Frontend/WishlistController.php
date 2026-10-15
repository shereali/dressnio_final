<?php

namespace App\Http\Controllers\API\Frontend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\ProductResource;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $customer = $request->user();
        $wishlists = Wishlist::with(['product.media', 'product.price', 'product.sizes', 'product.colors'])
            ->where('customer_id', $customer->id)
            ->get();

        return ApiResponse::success($wishlists);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $customer = $request->user();

        $wishlist = Wishlist::firstOrCreate([
            'customer_id' => $customer->id,
            'product_id'  => $request->product_id,
        ]);

        return ApiResponse::created($wishlist, 'Added to wishlist');
    }

    public function destroy(Request $request, int $productId): JsonResponse
    {
        $customer = $request->user();
        Wishlist::where('customer_id', $customer->id)->where('product_id', $productId)->delete();

        return ApiResponse::success(null, 'Removed from wishlist');
    }
}
