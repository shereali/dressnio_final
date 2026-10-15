<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SteadfastService
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected ?string $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('services.steadfast.base_url', 'https://portal.steadfast.com.bd/api/v1');
        $this->apiKey = env('STEADFAST_API_KEY');
        $this->secretKey = env('STEADFAST_SECRET_KEY');
    }

    protected function getApiKey(): ?string
    {
        return $this->apiKey ?: (class_exists(Setting::class) ? Setting::where('key', 'steadfast_api_key')->value('value') : null);
    }

    protected function getSecretKey(): ?string
    {
        return $this->secretKey ?: (class_exists(Setting::class) ? Setting::where('key', 'steadfast_secret_key')->value('value') : null);
    }

    /**
     * Create a consignment / parcel in SteadFast Courier.
     */
    public function createOrder(Order $order): array
    {
        $apiKey = $this->getApiKey();
        $secretKey = $this->getSecretKey();

        if (! $apiKey || ! $secretKey) {
            // Mock response if credentials are not yet configured in local/dev
            return [
                'status'      => 200,
                'message'     => 'Consignment created (Simulation Mode)',
                'consignment' => [
                    'consignment_id' => 'SF-' . rand(100000, 999999),
                    'tracking_code'  => 'STDF' . strtoupper(substr(md5($order->order_no . time()), 0, 8)),
                ],
            ];
        }

        try {
            $response = Http::withHeaders([
                'Api-Key'    => $this->apiKey,
                'Secret-Key' => $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/create_order", [
                'invoice'         => $order->order_no,
                'recipient_name'  => $order->customer_name,
                'recipient_phone' => $order->customer_phone,
                'recipient_address' => $order->delivery_address,
                'cod_amount'      => $order->payment_method === 'cod' ? (float) $order->total_amount : 0,
                'note'            => $order->delivery_notes ?? 'Handle with care - Dressnio Fashion',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Steadfast API Error: ' . $response->body());
            return [
                'status'  => $response->status(),
                'message' => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('Steadfast Service Exception: ' . $e->getMessage());
            return [
                'status'  => 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check consignment status in SteadFast.
     */
    public function checkStatus(string $consignmentId): array
    {
        $apiKey = $this->getApiKey();
        $secretKey = $this->getSecretKey();

        if (! $apiKey || ! $secretKey) {
            return [
                'status'         => 200,
                'delivery_status' => 'in_review',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Api-Key'    => $apiKey,
                'Secret-Key' => $secretKey,
            ])->get("{$this->baseUrl}/status_by_cid/{$consignmentId}");

            return $response->successful() ? $response->json() : ['status' => 400];
        } catch (\Throwable $e) {
            return ['status' => 500, 'message' => $e->getMessage()];
        }
    }
}
