<?php

namespace App\Http\Controllers\API\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderService;
use App\Services\SslcommerzService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * SSLCommerz return and IPN endpoints. A payment is only marked paid after the gateway's
 * validation API confirms the transaction, the amount and the currency.
 */
class PaymentController extends Controller
{
    public function __construct(
        protected SslcommerzService $gateway,
        protected OrderService $orders,
    ) {
    }

    private function frontend(string $orderNo, string $status): RedirectResponse
    {
        $base = rtrim((string) env('FRONTEND_URL', config('app.url')), '/');

        return redirect()->away("{$base}/checkout/result?" . http_build_query(['order' => $orderNo, 'status' => $status]));
    }

    private function payment(Request $request): ?Payment
    {
        $tran = (string) $request->input('tran_id');

        return $tran === '' ? null : Payment::where('tran_id', $tran)->where('provider', 'sslcommerz')->first();
    }

    /** Confirms a payment with the gateway. Safe to call twice (browser return and IPN). */
    private function settle(Payment $payment, Request $request): string
    {
        if ($payment->status === 'paid') {
            return 'paid';
        }

        $valId = (string) $request->input('val_id');
        $check = $valId !== '' ? $this->gateway->validate($valId) : null;

        $valid = $check
            && in_array($check['status'] ?? '', ['VALID', 'VALIDATED'], true)
            && ($check['tran_id'] ?? null) === $payment->tran_id
            && ($check['currency_type'] ?? $check['currency'] ?? 'BDT') === 'BDT'
            && abs((float) ($check['amount'] ?? 0) - $payment->amount) < 0.01;

        if (! $valid) {
            if ($payment->status === 'pending') {
                $payment->update(['status' => 'failed', 'payload' => $check ?: ['note' => 'validation failed']]);
            }

            return 'failed';
        }

        DB::transaction(function () use ($payment, $check) {
            $locked = Payment::lockForUpdate()->find($payment->id);

            if ($locked->status === 'paid') {
                return;
            }

            $locked->update([
                'status'      => 'paid',
                'gateway_ref' => $check['bank_tran_id'] ?? $check['val_id'] ?? null,
                'payload'     => $check,
                'paid_at'     => now(),
            ]);

            $order = Order::lockForUpdate()->find($locked->order_id);
            $order->payment_status = 'paid';
            $this->orders->addTimeline($order, $order->order_status, 'Online payment received.');
            $order->save();
        });

        return 'paid';
    }

    public function success(Request $request): RedirectResponse
    {
        $payment = $this->payment($request);

        if (! $payment) {
            return $this->frontend((string) $request->input('tran_id'), 'failed');
        }

        $status = $this->settle($payment, $request);

        return $this->frontend($payment->order->order_no, $status);
    }

    public function fail(Request $request): RedirectResponse
    {
        $payment = $this->payment($request);

        if ($payment && $payment->status === 'pending') {
            $payment->update(['status' => 'failed']);
        }

        return $this->frontend($payment?->order?->order_no ?? '', 'failed');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $payment = $this->payment($request);

        if ($payment && $payment->status === 'pending') {
            $payment->update(['status' => 'cancelled']);
        }

        return $this->frontend($payment?->order?->order_no ?? '', 'cancelled');
    }

    public function ipn(Request $request): JsonResponse
    {
        $payment = $this->payment($request);

        if ($payment) {
            $this->settle($payment, $request);
        }

        return response()->json(['received' => true]);
    }
}
