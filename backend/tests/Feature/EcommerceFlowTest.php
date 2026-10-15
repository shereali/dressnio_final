<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Price;
use App\Models\Stock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EcommerceFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    /**
     * Test complete storefront to checkout and tracking lifecycle.
     */
    public function test_storefront_homepage_and_catalog_apis(): void
    {
        // 1. Test Homepage API
        $response = $this->getJson('/api/client/homepage');
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'sliders',
                         'story',
                         'categories',
                         'latest',
                         'best_seller',
                         'testimonials',
                     ],
                 ]);

        // 2. Test Catalog API
        $catalogRes = $this->getJson('/api/client/products');
        $catalogRes->assertStatus(200)
                   ->assertJsonStructure([
                       'status',
                       'data',
                       'meta' => ['current_page', 'total'],
                   ]);
    }

    public function test_single_product_details_and_atomic_checkout(): void
    {
        $product = Product::with(['price', 'stocks'])->where('is_active', true)->first();
        $this->assertNotNull($product, 'At least one active demo product should exist');

        // 1. Test Single Product Details
        $detailRes = $this->getJson("/api/client/product-details/{$product->slug}");
        $detailRes->assertStatus(200)
                  ->assertJsonPath('data.id', $product->id);

        $initialStock = Stock::where('product_id', $product->id)->sum('quantity');

        // 2. Perform Checkout
        $checkoutPayload = [
            'name'           => 'Test Customer',
            'mobile_no'      => '01712345678',
            'email'          => 'test@example.com',
            'address'        => 'House 10, Road 2, Gulshan 1, Dhaka',
            'city'           => 'Dhaka',
            'payment_method' => 'cod',
            'items'          => [
                [
                    'id'       => $product->id,
                    'name'     => $product->name,
                    'quantity' => 1,
                    'price'    => (float) ($product->price->price ?? 1000),
                ],
            ],
        ];

        $checkoutRes = $this->postJson('/api/client/checkout', $checkoutPayload);
        $checkoutRes->assertStatus(201)
                    ->assertJsonPath('status', 'success')
                    ->assertJsonStructure([
                        'data' => [
                            'id',
                            'order_no',
                            'customer_name',
                            'total_amount',
                            'delivery_timeline',
                        ],
                    ]);

        $orderNo = $checkoutRes->json('data.order_no');
        $this->assertNotNull($orderNo);

        // 3. Verify Stock Decrement
        $newStock = Stock::where('product_id', $product->id)->sum('quantity');
        $this->assertEquals($initialStock - 1, $newStock, 'Stock must decrement atomically after checkout');

        // 4. Test Live Order Tracking
        $trackingRes = $this->getJson("/api/client/my-orders/{$orderNo}");
        $trackingRes->assertStatus(200)
                    ->assertJsonPath('data.order_no', $orderNo)
                    ->assertJsonPath('data.order_status', 'pending');
    }
}
