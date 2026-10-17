<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SteadFast Courier integration. Keys come from admin settings (encrypted),
 * with STEADFAST_API_KEY / STEADFAST_SECRET_KEY environment variables as a fallback.
 */
class SteadfastService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.steadfast.base_url', 'https://portal.steadfast.com.bd/api/v1'), '/');
    }

    protected function apiKey(): ?string
    {
        return Setting::read('steadfast_api_key') ?: env('STEADFAST_API_KEY') ?: null;
    }

    protected function secretKey(): ?string
    {
        return Setting::read('steadfast_secret_key') ?: env('STEADFAST_SECRET_KEY') ?: null;
    }

    public function isConfigured(): bool
    {
        return (bool) ($this->apiKey() && $this->secretKey());
    }

    protected function http()
    {
        return Http::timeout(15)->acceptJson()->withHeaders([
            'Api-Key'    => $this->apiKey(),
            'Secret-Key' => $this->secretKey(),
        ]);
    }

    /**
     * Book a parcel. Cash is collected only when the order has not been paid online.
     *
     * @return array{ok: bool, consignment_id: ?string, tracking_code: ?string, error: ?string}
     */
    public function createOrder(Order $order): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'consignment_id' => null, 'tracking_code' => null, 'error' => 'Add your SteadFast API key and secret key in settings first.'];
        }

        $phone = OrderService::normalizeMobile($order->customer_phone);
        $cod = $order->payment_status === 'paid' ? 0 : (float) $order->total_amount;

        try {
            $response = $this->http()->post("{$this->baseUrl}/create_order", [
                'invoice'           => $order->order_no,
                'recipient_name'    => $order->customer_name,
                'recipient_phone'   => $phone,
                'recipient_address' => $order->delivery_address,
                'cod_amount'        => $cod,
                'note'              => $order->delivery_notes ?: null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('SteadFast create_order failed', ['error' => class_basename($e)]);

            return ['ok' => false, 'consignment_id' => null, 'tracking_code' => null, 'error' => 'SteadFast could not be reached.'];
        }

        $json = $response->json() ?? [];

        if ($response->successful() && (int) ($json['status'] ?? 0) === 200 && ! empty($json['consignment'])) {
            return [
                'ok'             => true,
                'consignment_id' => (string) ($json['consignment']['consignment_id'] ?? ''),
                'tracking_code'  => (string) ($json['consignment']['tracking_code'] ?? ''),
                'error'          => null,
            ];
        }

        Log::warning('SteadFast rejected the parcel', ['order' => $order->order_no, 'body' => mb_substr($response->body(), 0, 300)]);

        $reason = is_array($json['errors'] ?? null) ? implode(' ', array_map(fn ($e) => implode(' ', (array) $e), $json['errors'])) : ($json['message'] ?? null);

        return ['ok' => false, 'consignment_id' => null, 'tracking_code' => null, 'error' => $reason ?: 'SteadFast refused the parcel.'];
    }

    /**
     * Current delivery status of a booked parcel (SteadFast wording, e.g. "in_review", "delivered").
     */
    public function checkStatus(string $consignmentId): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        try {
            $response = $this->http()->get("{$this->baseUrl}/status_by_cid/{$consignmentId}");
        } catch (\Throwable $e) {
            return null;
        }

        return $response->successful() ? ($response->json('delivery_status') ?: null) : null;
    }

    /** Maps SteadFast's delivery status to one of our order statuses, or null to leave the order as is. */
    public static function mapStatus(?string $status): ?string
    {
        return match (true) {
            $status === null                                                   => null,
            in_array($status, ['delivered', 'partial_delivered'], true)         => 'delivered',
            $status === 'cancelled'                                            => 'returned',
            default                                                            => null,
        };
    }
}
