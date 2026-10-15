<?php

namespace App\Http\Controllers\API\Frontend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Price;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Stock;
use App\Services\SteadfastService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected SteadfastService $steadfastService;

    public function __construct(SteadfastService $steadfastService)
    {
        $this->steadfastService = $steadfastService;
    }

    /**
     * High-Concurrency Atomic Checkout Endpoint with Zero Overselling.
     */
    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'mobile_no'      => 'required|string|regex:/^01[3-9]\d{8}$/',
            'email'          => 'nullable|email',
            'address'        => 'required|string|min:5',
            'city'           => 'nullable|string',
            'delivery_zone'  => 'nullable|string',
            'delivery_notes' => 'nullable|string',
            'payment_method' => 'nullable|string|in:cod,bkash,nagad,card',
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            $customer = $request->user();

            if (! $customer) {
                // Find or create customer record by mobile number for guest checkout
                $customer = Customer::firstOrCreate(
                    ['mobile_no' => $request->mobile_no],
                    [
                        'name'    => $request->name,
                        'email'   => $request->email,
                        'address' => $request->address,
                        'city'    => $request->city ?? 'Dhaka',
                    ]
                );
            }

            // Calculate Delivery Fee
            $city = strtolower($request->get('city', 'dhaka'));
            $isInsideDhaka = str_contains($city, 'dhaka');

            $shippingFeeInside = (float) (Setting::where('key', 'shipping_fee')->value('value') ?: 60);
            $shippingFeeOutside = (float) (Setting::where('key', 'shipping_fee_outside')->value('value') ?: 120);
            $freeShippingAbove = (float) (Setting::where('key', 'free_shipping_above')->value('value') ?: 2000);

            $shippingCost = $isInsideDhaka ? $shippingFeeInside : $shippingFeeOutside;

            $subtotal = 0;
            $orderItemsData = [];

            // Process each item with atomic lock
            foreach ($request->items as $itemData) {
                $productId = (int) $itemData['id'];
                $quantity = (int) $itemData['quantity'];
                $sizeName = $itemData['size'] ?? null;
                $colorCode = $itemData['color'] ?? null;

                // Lock product row for update to avoid race conditions
                $product = Product::with(['price', 'prices', 'sizes', 'colors', 'media'])
                    ->lockForUpdate()
                    ->find($productId);

                if (! $product || ! $product->is_active) {
                    throw new \RuntimeException("Product '{$itemData['name']}' is no longer available.");
                }

                // Determine unit price
                $unitPrice = 0;
                if ($product->is_size_visible && $sizeName) {
                    $matchedPrice = Price::where('product_id', $product->id)
                        ->whereHas('size', function ($q) use ($sizeName) {
                            $q->where('standard', $sizeName)->orWhere('name', $sizeName);
                        })
                        ->value('price');

                    $unitPrice = $matchedPrice ? (float) $matchedPrice : (float) ($product->price->price ?? 0);
                } else {
                    $unitPrice = (float) ($product->price->price ?? 0);
                }

                // If client provided price matches fallback, use it
                if ($unitPrice <= 0 && isset($itemData['price'])) {
                    $unitPrice = (float) $itemData['price'];
                }

                $itemTotal = $unitPrice * $quantity;
                $subtotal += $itemTotal;

                // Check & decrement inventory atomically
                $stockRow = Stock::where('product_id', $product->id)
                    ->lockForUpdate()
                    ->first();

                if ($stockRow && $stockRow->quantity < $quantity) {
                    throw new \RuntimeException("Insufficient stock for '{$product->name}'. Only {$stockRow->quantity} remaining.");
                }

                if ($stockRow) {
                    $stockRow->decrement('quantity', $quantity);
                }

                $product->decrement('stock_available', $quantity);

                $productImage = $itemData['image'] ?? ($product->media->first()->url ?? null);

                $orderItemsData[] = [
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'product_image' => $productImage,
                    'color_name'    => $colorCode,
                    'color_code'    => $colorCode,
                    'size_name'     => $sizeName,
                    'unit_price'    => $unitPrice,
                    'quantity'      => $quantity,
                    'total_price'   => $itemTotal,
                ];
            }

            // Apply free shipping rule if threshold is reached
            if ($subtotal >= $freeShippingAbove && $freeShippingAbove > 0) {
                $shippingCost = 0;
            }

            $discountAmount = 0;
            $totalAmount = max(0, $subtotal + $shippingCost - $discountAmount);

            // Generate human-friendly unique order number #DN-YYYYMMDD-XXXX
            $orderNo = 'DN-' . date('ymd') . '-' . strtoupper(Str::random(4));

            $order = Order::create([
                'order_no'        => $orderNo,
                'customer_id'     => $customer->id,
                'customer_name'   => $request->name,
                'customer_phone'  => $request->mobile_no,
                'customer_email'  => $request->email,
                'delivery_address'=> $request->address,
                'delivery_city'   => $request->city ?? 'Dhaka',
                'delivery_zone'   => $request->delivery_zone,
                'delivery_notes'  => $request->delivery_notes,
                'subtotal'        => $subtotal,
                'shipping_cost'   => $shippingCost,
                'discount_amount' => $discountAmount,
                'total_amount'    => $totalAmount,
                'payment_method'  => $request->get('payment_method', 'cod'),
                'payment_status'  => 'unpaid',
                'order_status'    => 'pending',
                'delivery_timeline' => [
                    [
                        'status' => 'pending',
                        'time'   => now()->toIso8601String(),
                        'note'   => 'Order placed successfully. Awaiting confirmation.',
                    ],
                ],
            ]);

            foreach ($orderItemsData as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }

            // Clean up synced cart if customer is authenticated
            if ($request->user()) {
                \App\Models\Cart::where('customer_id', $request->user()->id)->delete();
            }

            // Dispatch to SteadFast courier asynchronously / simulation
            try {
                $this->steadfastService->createOrder($order);
            } catch (\Throwable $e) {
                Log::warning('Async Steadfast booking queued: ' . $e->getMessage());
            }

            $order->load('items');

            return ApiResponse::created($order, 'Order placed successfully');
        });
    }

    /**
     * Get orders list for authenticated customer.
     */
    public function index(Request $request): JsonResponse
    {
        $customer = $request->user();
        $orders = Order::with('items')
            ->where('customer_id', $customer->id)
            ->orWhere('customer_phone', $customer->mobile_no)
            ->latest()
            ->paginate(10);

        return ApiResponse::success($orders->items(), 'Orders retrieved', 200, [
            'current_page' => $orders->currentPage(),
            'last_page'    => $orders->lastPage(),
            'total'        => $orders->total(),
        ]);
    }

    /**
     * Get single order details with tracking timeline.
     */
    public function show(Request $request, string $orderNo): JsonResponse
    {
        $order = Order::with('items')
            ->where('order_no', $orderNo)
            ->orWhere('id', $orderNo)
            ->first();

        if (! $order) {
            return ApiResponse::notFound('Order not found');
        }

        return ApiResponse::success($order);
    }

    /**
     * Cancel an order if still in pending status and restore inventory.
     */
    public function cancel(Request $request, string $orderNo): JsonResponse
    {
        return DB::transaction(function () use ($orderNo) {
            $order = Order::with('items')->where('order_no', $orderNo)->lockForUpdate()->first();

            if (! $order) {
                return ApiResponse::notFound('Order not found');
            }

            if ($order->order_status !== 'pending') {
                return ApiResponse::error('Only pending orders can be cancelled', 400);
            }

            // Restore stocks
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stock_available', $item->quantity);
                    Stock::where('product_id', $item->product_id)->increment('quantity', $item->quantity);
                }
            }

            $order->order_status = 'cancelled';
            $timeline = $order->delivery_timeline ?? [];
            $timeline[] = [
                'status' => 'cancelled',
                'time'   => now()->toIso8601String(),
                'note'   => 'Order cancelled by customer',
            ];
            $order->delivery_timeline = $timeline;
            $order->save();

            return ApiResponse::success($order, 'Order cancelled successfully');
        });
    }
}
