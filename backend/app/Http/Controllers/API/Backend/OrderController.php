<?php

namespace App\Http\Controllers\API\Backend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderService;
use App\Services\SteadfastService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /** Which status an order may move to from each status. */
    public const TRANSITIONS = [
        'pending'    => ['confirmed', 'processing', 'cancelled'],
        'confirmed'  => ['processing', 'shipped', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped'    => ['delivered', 'returned'],
        'delivered'  => ['returned'],
        'cancelled'  => [],
        'returned'   => [],
    ];

    public function __construct(
        protected SteadfastService $steadfast,
        protected OrderService $orders,
    ) {
    }

    /**
     * List orders with filters, plus a count per status for the tabs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with(['items', 'payments'])->latest();

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_no', 'LIKE', "%{$search}%")
                    ->orWhere('customer_phone', 'LIKE', "%{$search}%")
                    ->orWhere('customer_name', 'LIKE', "%{$search}%");
            });
        }

        $orders = $query->paginate(min(100, (int) $request->get('per_page', 15)));

        $counts = Order::select('order_status', DB::raw('count(*) as total'))->groupBy('order_status')->pluck('total', 'order_status');

        return ApiResponse::success($orders->items(), 'Orders retrieved successfully', 200, [
            'current_page' => $orders->currentPage(),
            'last_page'    => $orders->lastPage(),
            'per_page'     => $orders->perPage(),
            'total'        => $orders->total(),
            'counts'       => $counts,
            'needs_payment_review' => Payment::where('status', 'verifying')->count(),
        ]);
    }

    public function show(int|string $id): JsonResponse
    {
        $order = Order::with(['items', 'payments'])->where('id', $id)->orWhere('order_no', $id)->first();

        if (! $order) {
            return ApiResponse::notFound('Order not found');
        }

        return ApiResponse::success($order);
    }

    /**
     * Change the order status, payment status or notes. Moves that make no sense are refused.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'order_status'   => 'nullable|string|in:pending,confirmed,processing,shipped,delivered,cancelled,returned',
            'payment_status' => 'nullable|string|in:unpaid,paid,refunded',
            'delivery_notes' => 'nullable|string|max:500',
            'note'           => 'nullable|string|max:255',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $order = Order::with('items')->lockForUpdate()->find($id);

            if (! $order) {
                return ApiResponse::notFound('Order not found');
            }

            $new = $request->input('order_status');

            if ($new && $new !== $order->order_status) {
                if (! in_array($new, self::TRANSITIONS[$order->order_status] ?? [], true)) {
                    return ApiResponse::error("An order that is {$order->order_status} cannot be changed to {$new}.", 422);
                }

                if (in_array($new, ['cancelled', 'returned'], true)) {
                    $this->orders->restock($order);
                }

                $order->order_status = $new;
                $note = $request->input('note') ?: 'Status changed to ' . $new . ' by the store.';
                $this->orders->addTimeline($order, $new, $note);

                // Cash is collected when a cash-on-delivery parcel is handed over
                if ($new === 'delivered' && $order->payment_method === 'cod' && $order->payment_status === 'unpaid') {
                    $order->payment_status = 'paid';
                }
            }

            if ($request->filled('payment_status')) {
                $order->payment_status = $request->payment_status;
            }

            if ($request->has('delivery_notes')) {
                $order->delivery_notes = $request->delivery_notes;
            }

            $order->save();

            return ApiResponse::success($order->load('payments'), 'Order updated');
        });
    }

    /**
     * Accept or reject a bKash/Nagad payment the customer reported.
     */
    public function reviewPayment(Request $request, int $id, int $paymentId): JsonResponse
    {
        $request->validate(['action' => 'required|in:verify,reject']);

        return DB::transaction(function () use ($request, $id, $paymentId) {
            $order = Order::lockForUpdate()->find($id);
            $payment = Payment::where('order_id', $id)->where('id', $paymentId)->lockForUpdate()->first();

            if (! $order || ! $payment) {
                return ApiResponse::notFound('Payment not found');
            }

            if ($payment->status !== 'verifying') {
                return ApiResponse::error('This payment has already been reviewed.', 422);
            }

            if ($request->action === 'verify') {
                $payment->update(['status' => 'paid', 'paid_at' => now()]);
                $order->payment_status = 'paid';
                $this->orders->addTimeline($order, $order->order_status, ucfirst($payment->method) . " payment verified (TrxID {$payment->trx_id}).");
            } else {
                $payment->update(['status' => 'failed']);
                $stillChecking = $order->payments()->where('status', 'verifying')->where('id', '!=', $payment->id)->exists();
                $order->payment_status = $stillChecking ? 'verifying' : 'unpaid';
                $this->orders->addTimeline($order, $order->order_status, ucfirst($payment->method) . " payment could not be verified (TrxID {$payment->trx_id}).");
            }

            $order->save();

            return ApiResponse::success($order->load(['items', 'payments']), $request->action === 'verify' ? 'Payment verified' : 'Payment rejected');
        });
    }

    /**
     * Book the parcel with SteadFast. Only confirmed or processing orders that are paid or cash on delivery.
     */
    public function dispatchSteadfast(int $id): JsonResponse
    {
        $order = Order::with(['items', 'payments'])->find($id);

        if (! $order) {
            return ApiResponse::notFound('Order not found');
        }

        if ($order->steadfast_consignment_id) {
            return ApiResponse::error('This order is already booked with SteadFast.', 422);
        }

        if (! in_array($order->order_status, ['confirmed', 'processing'], true)) {
            return ApiResponse::error('Confirm the order before sending it to the courier.', 422);
        }

        if ($order->payment_method !== 'cod' && $order->payment_status !== 'paid') {
            return ApiResponse::error('Verify the payment before sending this order to the courier.', 422);
        }

        $result = $this->steadfast->createOrder($order);

        if (! $result['ok']) {
            return ApiResponse::error($result['error'] ?? 'SteadFast could not book this parcel.', 422);
        }

        $order->steadfast_consignment_id = $result['consignment_id'];
        $order->steadfast_tracking_code = $result['tracking_code'];
        $order->tracking_url = $result['tracking_code'] ? 'https://steadfast.com.bd/t/' . $result['tracking_code'] : null;
        $order->order_status = 'shipped';
        $this->orders->addTimeline($order, 'shipped', 'Handed to SteadFast Courier. Tracking code ' . $order->steadfast_tracking_code . '.');
        $order->save();

        return ApiResponse::success($order, 'Booked with SteadFast');
    }

    /**
     * Pull the latest delivery status from SteadFast for one order.
     */
    public function syncSteadfast(int $id): JsonResponse
    {
        $order = Order::find($id);

        if (! $order || ! $order->steadfast_consignment_id) {
            return ApiResponse::notFound('This order has not been booked with SteadFast.');
        }

        return ApiResponse::success($this->applyCourierStatus($order), 'Courier status refreshed');
    }

    /**
     * Refresh every parcel that is out for delivery.
     */
    public function syncAllShipped(): JsonResponse
    {
        $updated = 0;

        Order::where('order_status', 'shipped')->whereNotNull('steadfast_consignment_id')->get()->each(function (Order $order) use (&$updated) {
            $before = $order->order_status;

            if ($this->applyCourierStatus($order)->order_status !== $before) {
                $updated++;
            }
        });

        return ApiResponse::success(['updated' => $updated], $updated ? "{$updated} orders updated" : 'Everything is up to date');
    }

    private function applyCourierStatus(Order $order): Order
    {
        $status = $this->steadfast->checkStatus($order->steadfast_consignment_id);
        $mapped = SteadfastService::mapStatus($status);

        if ($mapped && $mapped !== $order->order_status && in_array($mapped, self::TRANSITIONS[$order->order_status] ?? [], true)) {
            DB::transaction(function () use ($order, $mapped, $status) {
                if ($mapped === 'returned') {
                    $this->orders->restock($order->loadMissing('items'));
                }

                $order->order_status = $mapped;
                $this->orders->addTimeline($order, $mapped, "SteadFast reports: {$status}.");

                if ($mapped === 'delivered' && $order->payment_method === 'cod' && $order->payment_status === 'unpaid') {
                    $order->payment_status = 'paid';
                }

                $order->save();
            });
        }

        return $order;
    }
}
