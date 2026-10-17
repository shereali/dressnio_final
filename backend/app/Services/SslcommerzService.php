<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SSLCommerz hosted checkout (cards, mobile banking, net banking).
 * Credentials come from admin settings; sandbox mode is on by default.
 */
class SslcommerzService
{
    public function isConfigured(): bool
    {
        return Setting::read('sslcommerz_enabled', '0') === '1'
            && (string) Setting::read('sslcommerz_store_id', '') !== ''
            && (string) Setting::read('sslcommerz_store_password', '') !== '';
    }

    private function host(): string
    {
        return Setting::read('sslcommerz_sandbox', '1') === '1'
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }

    /**
     * Start a hosted payment session.
     *
     * @return array{ok: bool, url: ?string, error: ?string}
     */
    public function initiate(Order $order, Payment $payment): array
    {
        $callback = rtrim((string) config('app.url'), '/') . '/api/payments/sslcommerz';

        try {
            $response = Http::asForm()->timeout(15)->post($this->host() . '/gwprocess/v4/api.php', [
                'store_id'         => Setting::read('sslcommerz_store_id'),
                'store_passwd'     => Setting::read('sslcommerz_store_password'),
                'total_amount'     => number_format($payment->amount, 2, '.', ''),
                'currency'         => 'BDT',
                'tran_id'          => $payment->tran_id,
                'success_url'      => "{$callback}/success",
                'fail_url'         => "{$callback}/fail",
                'cancel_url'       => "{$callback}/cancel",
                'ipn_url'          => "{$callback}/ipn",
                'cus_name'         => $order->customer_name,
                'cus_email'        => $order->customer_email ?: 'noreply@dressnio.com',
                'cus_add1'         => mb_substr($order->delivery_address, 0, 50),
                'cus_city'         => $order->delivery_city,
                'cus_country'      => 'Bangladesh',
                'cus_phone'        => $order->customer_phone,
                'shipping_method'  => 'Courier',
                'ship_name'        => $order->customer_name,
                'ship_add1'        => mb_substr($order->delivery_address, 0, 50),
                'ship_city'        => $order->delivery_city,
                'ship_country'     => 'Bangladesh',
                'product_name'     => 'Dressnio order ' . $order->order_no,
                'product_category' => 'Fashion',
                'product_profile'  => 'physical-goods',
            ]);
        } catch (\Throwable $e) {
            Log::warning('SSLCommerz initiate failed', ['error' => class_basename($e)]);

            return ['ok' => false, 'url' => null, 'error' => 'The payment gateway could not be reached.'];
        }

        $json = $response->json();

        if ($response->successful() && ($json['status'] ?? '') === 'SUCCESS' && ! empty($json['GatewayPageURL'])) {
            return ['ok' => true, 'url' => $json['GatewayPageURL'], 'error' => null];
        }

        Log::warning('SSLCommerz rejected the session', ['reason' => $json['failedreason'] ?? $response->status()]);

        return ['ok' => false, 'url' => null, 'error' => 'The payment gateway refused to start the payment.'];
    }

    /**
     * Ask SSLCommerz whether a payment really went through.
     *
     * @return array<string, mixed>|null the validation payload, or null when it could not be checked
     */
    public function validate(string $valId): ?array
    {
        try {
            $response = Http::timeout(15)->get($this->host() . '/validator/api/validationserverAPI.php', [
                'val_id'       => $valId,
                'store_id'     => Setting::read('sslcommerz_store_id'),
                'store_passwd' => Setting::read('sslcommerz_store_password'),
                'format'       => 'json',
            ]);
        } catch (\Throwable $e) {
            Log::warning('SSLCommerz validation failed', ['error' => class_basename($e)]);

            return null;
        }

        return $response->successful() ? $response->json() : null;
    }
}
