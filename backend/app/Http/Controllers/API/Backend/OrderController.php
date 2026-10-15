<?php

namespace App\Http\Controllers\API\Backend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\SteadfastService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected SteadfastService $steadfastService;

    public function __construct(SteadfastService $steadfastService)
    {
        $this->steadfastService = $steadfastService;
    }

    /**
     * List all customer orders with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::with('items')->latest();

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_no', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        return ApiResponse::success($orders->items(), 'Orders retrieved successfully', 200, [
            'current_page' => $orders->currentPage(),
            'last_page'    => $orders->lastPage(),
            'per_page'     => $orders->perPage(),
            'total'        => $orders->total(),
        ]);
    }

    /**
     * Show single order with items.
     */
    public function show(int|string $id): JsonResponse
    {
        $order = Order::with('items')->where('id', $id)->orWhere('order_no', $id)->first();

        if (! $order) {
            return ApiResponse::notFound('Order not found');
        }

        return ApiResponse::success($order);
    }

    /**
     * Update order status & delivery details.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $order = Order::with('items')->find($id);

        if (! $order) {
            return ApiResponse::notFound('Order not found');
        }

        $request->validate([
            'order_status'   => 'nullable|string|in:pending,confirmed,processing,shipped,delivered,cancelled,returned',
            'payment_status' => 'nullable|string|in:unpaid,paid,refunded',
            'delivery_notes' => 'nullable|string',
        ]);

        if ($request->filled('order_status')) {
            $order->order_status = $request->order_status;
            
            // Append to delivery timeline
            $timeline = $order->delivery_timeline ?? [];
            $timeline[] = [
                'status' => $request->order_status,
                'time'   => now()->toIso8601String(),
                'note'   => 'Status updated to ' . ucfirst($request->order_status) . ' by admin',
            ];
            $order->delivery_timeline = $timeline;
        }

        if ($request->filled('payment_status')) {
            $order->payment_status = $request->payment_status;
        }

        if ($request->filled('delivery_notes')) {
            $order->delivery_notes = $request->delivery_notes;
        }

        $order->save();

        return ApiResponse::success($order, 'Order updated successfully');
    }

    /**
     * Dispatch order to SteadFast Courier.
     */
    public function dispatchSteadfast(int $id): JsonResponse
    {
        $order = Order::with('items')->find($id);

        if (! $order) {
            return ApiResponse::notFound('Order not found');
        }

        $result = $this->steadfastService->createOrder($order);

        if ($result['status'] === 200 && isset($result['consignment'])) {
            $order->steadfast_consignment_id = $result['consignment']['consignment_id'] ?? null;
            $order->steadfast_tracking_code  = $result['consignment']['tracking_code'] ?? null;
            $order->order_status             = 'shipped';

            $timeline = $order->delivery_timeline ?? [];
            $timeline[] = [
                'status' => 'shipped',
                'time'   => now()->toIso8601String(),
                'note'   => 'Handed over to SteadFast Courier. Tracking Code: ' . $order->steadfast_tracking_code,
            ];
            $order->delivery_timeline = $timeline;
            $order->save();

            return ApiResponse::success($order, 'Order dispatched to SteadFast Courier successfully');
        }

        return ApiResponse::error('SteadFast Courier API dispatch failed', 400, $result);
    }
}
