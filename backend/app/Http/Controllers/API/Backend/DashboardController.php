<?php

namespace App\Http\Controllers\API\Backend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get aggregate stats and recent activity for Admin Dashboard.
     */
    public function stats(): JsonResponse
    {
        $totalOrders = Order::count();
        $pendingOrders = Order::where('order_status', 'pending')->count();
        $deliveredOrders = Order::where('order_status', 'delivered')->count();
        $totalRevenue = Order::whereIn('payment_status', ['paid'])->orWhere('order_status', 'delivered')->sum('total_amount');
        
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $totalCustomers = Customer::count();

        $lowStockItems = Stock::with(['product:id,name,slug', 'size:id,name,standard'])
            ->where('quantity', '<=', 5)
            ->limit(10)
            ->get();

        $recentOrders = Order::with('items')
            ->latest()
            ->limit(10)
            ->get();

        return ApiResponse::success([
            'overview' => [
                'total_revenue'    => (float) $totalRevenue,
                'total_orders'     => $totalOrders,
                'pending_orders'   => $pendingOrders,
                'delivered_orders' => $deliveredOrders,
                'total_products'   => $totalProducts,
                'active_products'  => $activeProducts,
                'total_customers'  => $totalCustomers,
            ],
            'low_stock_items' => $lowStockItems,
            'recent_orders'   => $recentOrders,
        ], 'Dashboard stats retrieved successfully');
    }
}
