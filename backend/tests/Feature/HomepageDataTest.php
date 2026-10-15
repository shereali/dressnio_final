<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_homepage_api_returns_all_essential_sections_with_images(): void
    {
        $response = $this->getJson('/api/client/homepage');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'sliders',
                    'story',
                    'categories',
                    'watch_n_buy',
                    'latest',
                    'best_seller',
                    'fashionist_choice',
                    'highlight',
                    'testimonials',
                    'posts',
                ],
            ]);

        $data = $response->json('data');

        $this->assertNotEmpty($data['sliders'], 'Sliders must not be empty');
        $this->assertNotNull($data['sliders'][0]['image'], 'First slider image must not be null');

        $this->assertNotEmpty($data['categories'], 'Categories must not be empty');
        $this->assertNotNull($data['categories'][0]['image'], 'First category image must not be null');

        $this->assertNotEmpty($data['latest'], 'Latest products must not be empty');
        $this->assertNotNull($data['latest'][0]['image'], 'First latest product image must not be null');

        $this->assertNotEmpty($data['best_seller'], 'Best seller products must not be empty');
        $this->assertNotNull($data['best_seller'][0]['image'], 'First best seller product image must not be null');

        $this->assertNotEmpty($data['story'], 'Stories must not be empty');
        $this->assertNotNull($data['story'][0]['image'], 'First story image must not be null');
    }
}
