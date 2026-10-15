<?php

namespace App\Http\Controllers\API\Frontend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get authenticated customer's synced cart items.
     */
    public function index(Request $request): JsonResponse
    {
        $customer = $request->user();
        $items = Cart::with(['product.media', 'product.price', 'size', 'color'])
            ->where('customer_id', $customer->id)
            ->get();

        return ApiResponse::success($items);
    }

    /**
     * Add item to synced cart.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'size_id'    => 'nullable|exists:sizes,id',
            'color_id'   => 'nullable|exists:colors,id',
            'quantity'   => 'nullable|integer|min:1',
        ]);

        $customer = $request->user();
        $quantity = $request->get('quantity', 1);

        $cartItem = Cart::where('customer_id', $customer->id)
            ->where('product_id', $request->product_id)
            ->where('size_id', $request->size_id)
            ->where('color_id', $request->color_id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            $cartItem = Cart::create([
                'customer_id' => $customer->id,
                'product_id'  => $request->product_id,
                'size_id'     => $request->size_id,
                'color_id'    => $request->color_id,
                'quantity'    => $quantity,
            ]);
        }

        $cartItem->load(['product.media', 'product.price', 'size', 'color']);

        return ApiResponse::success($cartItem, 'Item added to cart');
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $customer = $request->user();
        $cartItem = Cart::where('customer_id', $customer->id)->where('id', $id)->first();

        if (! $cartItem) {
            return ApiResponse::notFound('Cart item not found');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return ApiResponse::success($cartItem, 'Cart updated');
    }

    /**
     * Remove single item from cart.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $customer = $request->user();
        Cart::where('customer_id', $customer->id)->where('id', $id)->delete();

        return ApiResponse::success(null, 'Item removed from cart');
    }

    /**
     * Clear customer's cart.
     */
    public function clear(Request $request): JsonResponse
    {
        $customer = $request->user();
        Cart::where('customer_id', $customer->id)->delete();

        return ApiResponse::success(null, 'Cart cleared');
    }
}
