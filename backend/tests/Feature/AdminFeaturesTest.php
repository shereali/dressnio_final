<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Color;
use App\Models\ColorSet;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $loginRes = $this->postJson('/api/admin-login', [
            'email'    => 'admin@dressnio.com',
            'password' => 'dressnio@2026',
        ]);

        $this->token = $loginRes->json('data.token');
    }

    protected function authHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->token}",
            'Accept'        => 'application/json',
        ];
    }

    public function test_category_management_crud(): void
    {
        // 1. Create Category
        $createRes = $this->withHeaders($this->authHeaders())
                          ->postJson('/api/category', [
                              'name'        => 'Embroidered Festive Wear',
                              'description' => 'Luxury festive collection',
                              'is_active'   => true,
                              'is_featured' => true,
                              'order'       => 1,
                          ]);

        $createRes->assertStatus(201);
        $categoryId = $createRes->json('data.id') ?? $createRes->json('id');
        $this->assertDatabaseHas('categories', ['name' => 'Embroidered Festive Wear']);

        // 2. Fetch Category List
        $listRes = $this->withHeaders($this->authHeaders())->getJson('/api/category');
        $listRes->assertStatus(200);

        // 3. Update Category
        $updateRes = $this->withHeaders($this->authHeaders())
                          ->putJson("/api/category/{$categoryId}", [
                              'name'        => 'Embroidered Festive Wear V2',
                              'is_active'   => true,
                              'is_featured' => false,
                          ]);
        $updateRes->assertStatus(200);
        $this->assertDatabaseHas('categories', ['name' => 'Embroidered Festive Wear V2']);

        // 4. Delete Category
        $deleteRes = $this->withHeaders($this->authHeaders())->deleteJson("/api/category/{$categoryId}");
        $deleteRes->assertStatus(200);
    }

    public function test_color_management_crud(): void
    {
        // 1. Create Color Set with child colors
        $createRes = $this->withHeaders($this->authHeaders())
                          ->postJson('/api/color', [
                              'name'       => 'Royal Pastel Set',
                              'color_name' => ['Baby Pink', 'Sky Blue'],
                              'color_code' => ['#F4C2C2', '#87CEEB'],
                          ]);

        $createRes->assertSuccessful();
        $this->assertDatabaseHas('color_sets', ['name' => 'Royal Pastel Set']);
        $this->assertDatabaseHas('colors', ['color_code' => '#F4C2C2']);

        // 2. Fetch Color Sets
        $listRes = $this->withHeaders($this->authHeaders())->getJson('/api/color');
        $listRes->assertStatus(200);
    }

    public function test_size_management_crud(): void
    {
        // 1. Create Kameez standard sizes
        $createRes = $this->withHeaders($this->authHeaders())
                          ->postJson('/api/size', [
                              'size_type' => 1,
                              'standard'  => ['3XL', '4XL'],
                              'bust'      => [48, 50],
                              'waist'     => [44, 46],
                              'hip'       => [52, 54],
                          ]);

        $createRes->assertSuccessful();
        $this->assertDatabaseHas('sizes', ['standard' => '3XL', 'size_type' => 1]);

        // 2. Fetch Sizes
        $listRes = $this->withHeaders($this->authHeaders())->getJson('/api/size');
        $listRes->assertStatus(200);
    }

    public function test_product_creation_and_lifecycle(): void
    {
        $category = Category::first();
        $color = Color::first();
        $size = Size::where('size_type', 1)->first();

        $createRes = $this->withHeaders($this->authHeaders())
                          ->postJson('/api/products', [
                              'name'                => 'Elegance Organza 2-Piece',
                              'description'         => 'Premium organza fabric with hand-work embroidery.',
                              'is_active'           => true,
                              'is_featured'         => true,
                              'new_arrival'         => true,
                              'best_seller'         => false,
                              'highlight'           => true,
                              'fashionist_choice'   => true,
                              'has_color_variation' => true,
                              'cloth_type'          => 2, // Single Kameez
                              'fabric_type'         => 1, // Stitched
                              'stock_status'        => 1, // In Stock
                              'category_id'         => [$category->id],
                              'color_id'            => [$color->id],
                              'kameez_size_id'      => [$size->id],
                              'price'               => [4500],
                              'discount'            => [200],
                              'quantity'            => [25],
                          ]);

        $createRes->assertSuccessful();
        $productId = $createRes->json('data.id') ?? $createRes->json('id');
        $this->assertDatabaseHas('products', ['name' => 'Elegance Organza 2-Piece', 'stock_available' => 25]);

        // Update product
        $updateRes = $this->withHeaders($this->authHeaders())
                          ->putJson("/api/products/{$productId}", [
                              'name'                => 'Elegance Organza 2-Piece Updated',
                              'description'         => 'Updated luxury collection piece.',
                              'is_active'           => true,
                              'cloth_type'          => 2,
                              'fabric_type'         => 1,
                              'stock_status'        => 1,
                              'category_id'         => [$category->id],
                              'color_id'            => [$color->id],
                              'kameez_size_id'      => [$size->id],
                              'price'               => [4800],
                              'discount'            => [0],
                              'quantity'            => [40],
                          ]);

        $updateRes->assertSuccessful();
        $this->assertDatabaseHas('products', ['name' => 'Elegance Organza 2-Piece Updated', 'stock_available' => 40]);
    }
}
