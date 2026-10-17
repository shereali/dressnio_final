<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Price;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderPaymentCourierTest extends TestCase
{
    use RefreshDatabase;

    private array $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        foreach (['bkash', 'nagad'] as $wallet) {
            Setting::write("payment_{$wallet}_enabled", '1');
            Setting::write("{$wallet}_number", '01712345670');
        }

        $token = $this->postJson('/api/admin-login', ['email' => 'admin@dressnio.com', 'password' => 'dressnio@2026'])->json('data.token');
        $this->admin = ['Authorization' => "Bearer {$token}", 'Accept' => 'application/json'];
    }

    private function product(): Product
    {
        return Product::with('sizes')->where('is_active', true)->whereHas('price')->first();
    }

    private function payload(Product $product, array $extra = []): array
    {
        return array_merge([
            'name'           => 'Ayesha Rahman',
            'mobile_no'      => '01712345678',
            'address'        => 'House 10, Road 2, Gulshan 1, Dhaka',
            'city'           => 'Dhaka',
            'payment_method' => 'cod',
            'items'          => [['id' => $product->id, 'quantity' => 1, 'size' => $product->sizes->first()?->standard, 'price' => 1]],
        ], $extra);
    }

    private function place(array $extra = []): array
    {
        $res = $this->postJson('/api/client/checkout', $this->payload($this->product(), $extra));
        $res->assertStatus(201);

        return $res->json('data');
    }

    public function test_customer_token_cannot_use_admin_endpoints(): void
    {
        $customer = Customer::create(['name' => 'Buyer', 'mobile_no' => '01712345678', 'is_active' => true]);
        $token = $customer->createToken('customer-token', ['customer'])->plainTextToken;
        $headers = ['Authorization' => "Bearer {$token}", 'Accept' => 'application/json'];

        $this->getJson('/api/settings', $headers)->assertStatus(403);
        app('auth')->forgetGuards();
        $this->getJson('/api/orders', $headers)->assertStatus(403);
        app('auth')->forgetGuards();
        $this->getJson('/api/client/me', $headers)->assertOk();
        app('auth')->forgetGuards();
        $this->getJson('/api/settings', $this->admin)->assertOk();
    }

    public function test_checkout_charges_the_database_price_with_discount_not_the_client_price(): void
    {
        $product = $this->product();
        $size = $product->sizes->first()?->standard;
        $row = Price::where('product_id', $product->id)->first();
        $row->update(['discount' => 20]);
        $expected = round($row->price * 0.8, 2);

        $order = $this->place();

        $this->assertEquals($expected, $order['items'][0]['unit_price']);
        $this->assertNotEquals(1.0, $order['items'][0]['unit_price']);
    }

    public function test_tracking_needs_the_phone_number_on_the_order(): void
    {
        $order = $this->place();

        $this->getJson("/api/client/my-orders/{$order['order_no']}")->assertStatus(404);
        $this->getJson("/api/client/my-orders/{$order['order_no']}?mobile_no=01999999999")->assertStatus(404);
        $this->getJson("/api/client/my-orders/{$order['order_no']}?mobile_no=%2B8801712345678")->assertOk();
    }

    public function test_only_the_owner_can_cancel_and_stock_is_restored(): void
    {
        $product = $this->product();
        $before = Stock::where('product_id', $product->id)->sum('quantity');
        $order = $this->place();
        $this->assertEquals($before - 1, Stock::where('product_id', $product->id)->sum('quantity'));

        $this->postJson("/api/client/my-orders/{$order['order_no']}/cancel", ['mobile_no' => '01888888888'])->assertStatus(404);
        $this->postJson("/api/client/my-orders/{$order['order_no']}/cancel", ['mobile_no' => '01712345678'])->assertOk();

        $this->assertEquals($before, Stock::where('product_id', $product->id)->sum('quantity'));
    }

    public function test_bkash_needs_a_trx_id_and_a_trx_id_cannot_be_reused(): void
    {
        $product = $this->product();

        $this->postJson('/api/client/checkout', $this->payload($product, ['payment_method' => 'bkash']))->assertStatus(422);

        $extra = ['payment_method' => 'bkash', 'trx_id' => 'ab12cd34ef', 'sender_number' => '01712345678'];
        $order = $this->place($extra);
        $this->assertSame('verifying', $order['payment_status']);

        $this->postJson('/api/client/checkout', $this->payload($product, $extra))->assertStatus(422)->assertJsonValidationErrors('trx_id');
    }

    public function test_admin_verifying_a_manual_payment_marks_the_order_paid(): void
    {
        $order = $this->place(['payment_method' => 'nagad', 'trx_id' => 'NG12345678', 'sender_number' => '01712345678']);
        $payment = Payment::where('order_id', $order['id'])->first();

        $this->postJson("/api/orders/{$order['id']}/payments/{$payment->id}/review", ['action' => 'verify'], $this->admin)->assertOk();

        $this->assertSame('paid', Order::find($order['id'])->payment_status);
        $this->assertSame('paid', $payment->fresh()->status);
    }

    public function test_card_payment_starts_on_sslcommerz_and_is_marked_paid_only_after_validation(): void
    {
        Setting::write('sslcommerz_enabled', '1');
        Setting::write('sslcommerz_store_id', 'teststore');
        Setting::write('sslcommerz_store_password', 'teststore@ssl');

        Http::fake(['*/gwprocess/v4/api.php' => Http::response(['status' => 'SUCCESS', 'GatewayPageURL' => 'https://sandbox.sslcommerz.com/pay/abc'], 200)]);

        $order = $this->place(['payment_method' => 'card']);
        $this->assertSame('https://sandbox.sslcommerz.com/pay/abc', $order['payment_url']);

        $payment = Payment::where('order_id', $order['id'])->first();

        $good = ['status' => 'VALID', 'tran_id' => $payment->tran_id, 'amount' => number_format($payment->amount, 2, '.', ''), 'currency_type' => 'BDT', 'bank_tran_id' => 'BANK1'];
        Http::fakeSequence('*/validationserverAPI.php*')
            ->push(['status' => 'VALID', 'tran_id' => $payment->tran_id, 'amount' => '1.00', 'currency_type' => 'BDT'], 200)
            ->push($good, 200);

        // A return whose validated amount does not match must not mark the order paid
        $this->post('/api/payments/sslcommerz/success', ['tran_id' => $payment->tran_id, 'val_id' => 'v1'])->assertRedirect();
        $this->assertSame('unpaid', Order::find($order['id'])->payment_status);

        // A matching validation does
        $payment->update(['status' => 'pending']);
        $this->post('/api/payments/sslcommerz/success', ['tran_id' => $payment->tran_id, 'val_id' => 'v2'])->assertRedirect();
        $this->assertSame('paid', Order::find($order['id'])->payment_status);
    }

    public function test_steadfast_booking_rules_and_booking(): void
    {
        $order = $this->place();
        $id = $order['id'];

        // Not confirmed yet
        $this->postJson("/api/orders/{$id}/steadfast/dispatch", [], $this->admin)->assertStatus(422);

        $this->putJson("/api/orders/{$id}", ['order_status' => 'confirmed'], $this->admin)->assertOk();

        // No keys: refuses instead of pretending to book
        $this->postJson("/api/orders/{$id}/steadfast/dispatch", [], $this->admin)->assertStatus(422);
        $this->assertNull(Order::find($id)->steadfast_consignment_id);

        Setting::write('steadfast_api_key', 'key');
        Setting::write('steadfast_secret_key', 'secret');
        Http::fake(['*/create_order' => Http::response(['status' => 200, 'consignment' => ['consignment_id' => 1234, 'tracking_code' => 'TRK1']], 200)]);

        $this->postJson("/api/orders/{$id}/steadfast/dispatch", [], $this->admin)->assertOk();

        $fresh = Order::find($id);
        $this->assertSame('shipped', $fresh->order_status);
        $this->assertSame('TRK1', $fresh->steadfast_tracking_code);
        Http::assertSent(fn ($r) => str_ends_with($r->url(), '/create_order') && (float) $r['cod_amount'] === (float) $fresh->total_amount);

        // Courier reports delivery
        Http::fake(['*/status_by_cid/*' => Http::response(['status' => 200, 'delivery_status' => 'delivered'], 200)]);
        $this->postJson("/api/orders/{$id}/steadfast/sync", [], $this->admin)->assertOk();
        $this->assertSame('delivered', Order::find($id)->order_status);
        $this->assertSame('paid', Order::find($id)->payment_status);
    }

    public function test_illegal_status_changes_are_refused(): void
    {
        $order = $this->place();
        $this->putJson("/api/orders/{$order['id']}", ['order_status' => 'delivered'], $this->admin)->assertStatus(422);
        $this->putJson("/api/orders/{$order['id']}", ['order_status' => 'cancelled'], $this->admin)->assertOk();
        $this->putJson("/api/orders/{$order['id']}", ['order_status' => 'confirmed'], $this->admin)->assertStatus(422);
    }
}
