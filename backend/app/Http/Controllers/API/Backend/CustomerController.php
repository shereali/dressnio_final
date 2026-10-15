<?php

namespace App\Http\Controllers\API\Backend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Customer::withCount('orders')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('mobile_no', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
        }

        $perPage = (int) $request->get('per_page', 15);
        $customers = $query->paginate($perPage);

        return ApiResponse::success($customers->items(), 'Customers fetched successfully', 200, [
            'current_page' => $customers->currentPage(),
            'last_page'    => $customers->lastPage(),
            'total'        => $customers->total(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $customer = Customer::with('orders.items')->find($id);

        if (! $customer) {
            return ApiResponse::notFound('Customer not found');
        }

        return ApiResponse::success($customer);
    }
}
