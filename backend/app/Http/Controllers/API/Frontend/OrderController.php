<?php

namespace App\Http\Controllers\API\Frontend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Setting;
use App\Services\OrderService;
use App\Services\PaymentMethods;
use App\Services\SslcommerzService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orders,
        protected SslcommerzService $sslcommerz,
    ) {
    }

    /** The signed-in customer, if the request carries a customer token. */
    private function customer(Request $request): ?Customer
    {
        $user = $request->user('sanctum');

        return $user instanceof Customer ? $user : null;
    }

    /**
     * Finds an order the requester may see: the owner (signed in), or someone who knows the
     * order number and the phone number it was placed with. Anything else looks like "not found".
     */
    private function ownedOrder(Request $request, string $orderNo): ?Order
    {
        $order = Order::where('order_no', $orderNo)->first();

        if (! $order) {
            return null;
        }

        $customer = $this->customer($request);

        if ($customer && ($order->customer_id === $customer->id
            || OrderService::normalizeMobile($order->customer_phone) === OrderService::normalizeMobile($customer->mobile_no))) {
            return $order;
        }

        $phone = OrderService::normalizeMobile($request->input('mobile_no'));

        if ($phone !== '' && hash_equals(OrderService::normalizeMobile($order->customer_phone), $phone)) {
            return $order;
        }

        return null;
    }

    /**
     * Atomic checkout: prices come from the database, stock is locked and reserved.
     */
    public function checkout(Request $request): JsonResponse
    {
        $request->merge(['mobile_no' => OrderService::normalizeMobile($request->input('mobile_no'))]);

        $methods = PaymentMethods::enabled();

        $request->validate([
            'name'              => 'required|string|max:255',
            'mobile_no'         => ['required', 'string', 'regex:/^01[3-9]\d{8}$/'],
            'email'             => 'nullable|email|max:255',
            'address'           => 'required|string|min:5|max:500',
            'city'              => 'nullable|string|max:100',
            'delivery_zone'     => 'nullable|string|max:100',
            'delivery_notes'    => 'nullable|string|max:500',
            'payment_method'    => ['nullable', 'string', 'in:' . implode(',', array_keys($methods) ?: ['cod'])],
            'trx_id'            => ['nullable', 'string', 'regex:/^[A-Za-z0-9]{6,20}$/'],
            'sender_number'     => ['nullable', 'string', 'regex:/^01[3-9]\d{8}$/'],
            'items'             => 'required|array|min:1|max:30',
            'items.*.id'        => 'required|exists:products,id',
            'items.*.quantity'  => 'required|integer|min:1|max:20',
        ], [
            'mobile_no.regex'      => 'Enter a valid Bangladeshi mobile number, like 01712345678.',
            'trx_id.regex'         => 'The TrxID has 6 to 20 letters and numbers.',
            'sender_number.regex'  => 'Enter the number you paid from, like 01712345678.',
            'payment_method.in'    => 'That payment method is not available. Choose another.',
        ]);

        $method = $request->input('payment_method', array_key_first($methods) ?: 'cod');
        $manual = in_array($method, ['bkash', 'nagad'], true);

        if ($manual) {
            if (! $request->filled('trx_id') || ! $request->filled('sender_number')) {
                throw ValidationException::withMessages(['trx_id' => ['Enter the TrxID and the number you paid from.']]);
            }

            $this->assertTrxUnused($method, strtoupper($request->trx_id));
        }

        $freeFrom = (float) Setting::read('free_shipping_above', 2000);
        $feeInside = (float) Setting::read('shipping_fee', 60);
        $feeOutside = (float) Setting::read('shipping_fee_outside', 120);

        try {
            $order = DB::transaction(function () use ($request, $method, $manual, $freeFrom, $feeInside, $feeOutside) {
                $customer = $this->customer($request) ?: Customer::firstOrCreate(
                    ['mobile_no' => $request->mobile_no],
                    [
                        'name'      => $request->name,
                        'email'     => null,
                        'address'   => $request->address,
                        'city'      => $request->city ?? 'Dhaka',
                        'is_active' => true,
                    ]
                );

                $inside = str_contains(strtolower((string) ($request->city ?? 'dhaka')), 'dhaka')
                    && ! str_contains(strtolower((string) $request->city), 'outside');
                $shipping = $inside ? $feeInside : $feeOutside;

                $subtotal = 0.0;
                $lines = [];

                foreach ($request->items as $item) {
                    $product = Product::with('sizes')->lockForUpdate()->find((int) $item['id']);

                    if (! $product || ! $product->is_active) {
                        throw new \RuntimeException('One of the items in your bag is no longer available.');
                    }

                    $sizeName = $item['size'] ?? null;
                    $qty = (int) $item['quantity'];

                    if ($product->is_size_visible && $product->sizes->isNotEmpty() && ! $sizeName) {
                        throw new \RuntimeException("Choose a size for {$product->name}.");
                    }

                    $row = $this->orders->priceRow($product, $sizeName);
                    $unit = $this->orders->unitPrice($row);

                    if ($unit <= 0) {
                        throw new \RuntimeException("{$product->name} cannot be ordered right now.");
                    }

                    $stock = $this->orders->stockRow($product->id, $row, true);
                    $available = $stock ? (int) $stock->quantity : (int) $product->stock_available;

                    if ($available < $qty) {
                        throw new \RuntimeException("Only {$available} left of {$product->name}. Reduce the quantity and try again.");
                    }

                    $stock?->decrement('quantity', $qty);
                    $product->decrement('stock_available', $qty);

                    $lineTotal = $unit * $qty;
                    $subtotal += $lineTotal;

                    $lines[] = [
                        'product_id'    => $product->id,
                        'product_name'  => $product->name,
                        'product_image' => $item['image'] ?? null,
                        'color_name'    => $item['color'] ?? null,
                        'color_code'    => $item['color'] ?? null,
                        'size_name'     => $sizeName,
                        'unit_price'    => $unit,
                        'quantity'      => $qty,
                        'total_price'   => $lineTotal,
                    ];
                }

                if ($freeFrom > 0 && $subtotal >= $freeFrom) {
                    $shipping = 0.0;
                }

                $total = $subtotal + $shipping;

                $order = Order::create([
                    'order_no'          => 'DN-' . date('ymd') . '-' . strtoupper(Str::random(4)),
                    'customer_id'       => $customer->id,
                    'customer_name'     => $request->name,
                    'customer_phone'    => $request->mobile_no,
                    'customer_email'    => $request->email,
                    'delivery_address'  => $request->address,
                    'delivery_city'     => $request->city ?? 'Dhaka',
                    'delivery_zone'     => $request->delivery_zone,
                    'delivery_notes'    => $request->delivery_notes,
                    'subtotal'          => $subtotal,
                    'shipping_cost'     => $shipping,
                    'discount_amount'   => 0,
                    'total_amount'      => $total,
                    'payment_method'    => $method,
                    'payment_status'    => $manual ? 'verifying' : 'unpaid',
                    'order_status'      => 'pending',
                ]);

                $this->orders->addTimeline($order, 'pending', 'Order placed. We will confirm it by phone or SMS.');
                $order->save();

                foreach ($lines as $line) {
                    OrderItem::create($line + ['order_id' => $order->id]);
                }

                if ($manual) {
                    Payment::create([
                        'order_id'      => $order->id,
                        'customer_id'   => $customer->id,
                        'method'        => $method,
                        'provider'      => 'manual',
                        'amount'        => $total,
                        'status'        => 'verifying',
                        'trx_id'        => strtoupper($request->trx_id),
                        'sender_number' => $request->sender_number,
                    ]);
                } elseif ($method === 'card') {
                    Payment::create([
                        'order_id'    => $order->id,
                        'customer_id' => $customer->id,
                        'method'      => 'card',
                        'provider'    => 'sslcommerz',
                        'amount'      => $total,
                        'status'      => 'pending',
                        'tran_id'     => $order->order_no . '-' . strtoupper(Str::random(5)),
                    ]);
                }

                if ($this->customer($request)) {
                    Cart::where('customer_id', $customer->id)->delete();
                }

                return $order;
            });
        } catch (\RuntimeException $e) {
            return ApiResponse::error($e->getMessage(), 422);
        }

        $order->load('items');

        // Card payments continue on the gateway's hosted page; the HTTP call stays outside the transaction.
        if ($method === 'card') {
            $order->setAttribute('payment_url', $this->startCardPayment($order));
        }

        return ApiResponse::created($order, 'Order placed successfully');
    }

    private function assertTrxUnused(string $method, string $trxId): void
    {
        $used = Payment::where('method', $method)
            ->where('trx_id', $trxId)
            ->whereNotIn('status', ['failed', 'cancelled'])
            ->exists();

        if ($used) {
            throw ValidationException::withMessages(['trx_id' => ['This TrxID was already used on another order. Check it and try again.']]);
        }
    }

    private function startCardPayment(Order $order): ?string
    {
        $payment = $order->payments()->where('provider', 'sslcommerz')->where('status', 'pending')->latest('id')->first();

        if (! $payment) {
            return null;
        }

        $result = $this->sslcommerz->initiate($order, $payment);

        return $result['ok'] ? $result['url'] : null;
    }

    /**
     * Orders for the signed-in customer.
     */
    public function index(Request $request): JsonResponse
    {
        $customer = $request->user();

        $orders = Order::with(['items', 'payments:id,order_id,method,status,trx_id'])
            ->where(function ($q) use ($customer) {
                $q->where('customer_id', $customer->id)->orWhere('customer_phone', $customer->mobile_no);
            })
            ->latest()
            ->paginate(10);

        return ApiResponse::success($orders->items(), 'Orders retrieved', 200, [
            'current_page' => $orders->currentPage(),
            'last_page'    => $orders->lastPage(),
            'total'        => $orders->total(),
        ]);
    }

    /**
     * One order with its delivery timeline. Needs the owner's sign-in, or the phone number on the order.
     */
    public function show(Request $request, string $orderNo): JsonResponse
    {
        $order = $this->ownedOrder($request, $orderNo);

        if (! $order) {
            return ApiResponse::notFound('We could not find that order. Check the order number and the phone number you ordered with.');
        }

        $order->load(['items', 'payments:id,order_id,method,status,trx_id']);

        return ApiResponse::success($order);
    }

    /**
     * Cancel a pending order that has not been paid online, and put the stock back.
     */
    public function cancel(Request $request, string $orderNo): JsonResponse
    {
        $found = $this->ownedOrder($request, $orderNo);

        if (! $found) {
            return ApiResponse::notFound('Order not found');
        }

        return DB::transaction(function () use ($found) {
            $order = Order::with('items')->lockForUpdate()->find($found->id);

            if ($order->order_status !== 'pending') {
                return ApiResponse::error('Only pending orders can be cancelled. Contact us if you need help with this one.', 422);
            }

            if (in_array($order->payment_status, ['paid', 'verifying'], true)) {
                return ApiResponse::error('This order has a payment on it. Contact us and we will cancel it and refund you.', 422);
            }

            $this->orders->restock($order);

            $order->order_status = 'cancelled';
            $this->orders->addTimeline($order, 'cancelled', 'Order cancelled by the customer.');
            $order->save();

            $order->payments()->whereIn('status', ['pending'])->update(['status' => 'cancelled']);

            return ApiResponse::success($order, 'Order cancelled');
        });
    }

    /**
     * Retry paying a card order that is still unpaid.
     */
    public function pay(Request $request, string $orderNo): JsonResponse
    {
        $order = $this->ownedOrder($request, $orderNo);

        if (! $order) {
            return ApiResponse::notFound('Order not found');
        }

        if ($order->order_status !== 'pending' || $order->payment_status !== 'unpaid' || $order->payment_method !== 'card') {
            return ApiResponse::error('This order does not need an online payment.', 422);
        }

        if (! $this->sslcommerz->isConfigured()) {
            return ApiResponse::error('Online payment is not available right now.', 503);
        }

        $order->payments()->where('provider', 'sslcommerz')->where('status', 'pending')->update(['status' => 'cancelled']);

        Payment::create([
            'order_id'    => $order->id,
            'customer_id' => $order->customer_id,
            'method'      => 'card',
            'provider'    => 'sslcommerz',
            'amount'      => $order->total_amount,
            'status'      => 'pending',
            'tran_id'     => $order->order_no . '-' . strtoupper(Str::random(5)),
        ]);

        $url = $this->startCardPayment($order);

        return $url
            ? ApiResponse::success(['payment_url' => $url], 'Payment started')
            : ApiResponse::error('We could not open the payment page. Try again in a moment.', 502);
    }

    /**
     * Send the TrxID for a bKash or Nagad payment made after the order was placed.
     */
    public function submitPayment(Request $request, string $orderNo): JsonResponse
    {
        $order = $this->ownedOrder($request, $orderNo);

        if (! $order) {
            return ApiResponse::notFound('Order not found');
        }

        $request->validate([
            'method'        => 'required|in:bkash,nagad',
            'trx_id'        => ['required', 'string', 'regex:/^[A-Za-z0-9]{6,20}$/'],
            'sender_number' => ['required', 'string', 'regex:/^01[3-9]\d{8}$/'],
        ], [
            'trx_id.regex'        => 'The TrxID has 6 to 20 letters and numbers.',
            'sender_number.regex' => 'Enter the number you paid from, like 01712345678.',
        ]);

        if ($order->payment_status === 'paid' || in_array($order->order_status, ['cancelled', 'returned'], true)) {
            return ApiResponse::error('This order cannot take a payment.', 422);
        }

        if (! array_key_exists($request->method, PaymentMethods::enabled())) {
            return ApiResponse::error('That payment method is not available.', 422);
        }

        $trx = strtoupper($request->trx_id);
        $this->assertTrxUnused($request->method, $trx);

        Payment::create([
            'order_id'      => $order->id,
            'customer_id'   => $order->customer_id,
            'method'        => $request->method,
            'provider'      => 'manual',
            'amount'        => $order->total_amount,
            'status'        => 'verifying',
            'trx_id'        => $trx,
            'sender_number' => $request->sender_number,
        ]);

        $order->update(['payment_method' => $request->method, 'payment_status' => 'verifying']);

        return ApiResponse::success($order->fresh('payments'), 'Payment received. We will confirm it shortly.');
    }
}
