<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/admin-login', [
            'email'    => 'admin@dressnio.com',
            'password' => 'dressnio@2026',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonStructure([
                     'data' => [
                         'token',
                         'user' => ['id', 'name', 'email', 'role'],
                     ],
                 ]);

        $token = $response->json('data.token');
        $this->assertNotEmpty($token);

        // Test authenticated admin access to dashboard stats
        $statsRes = $this->withHeader('Authorization', "Bearer {$token}")
                         ->getJson('/api/dashboard/stats');

        $statsRes->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => ['overview', 'recent_orders', 'low_stock_items'],
                 ]);
    }

    public function test_admin_login_rejects_invalid_password(): void
    {
        $response = $this->postJson('/api/admin-login', [
            'email'    => 'admin@dressnio.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
                 ->assertJsonPath('status', 'error');
    }
}
