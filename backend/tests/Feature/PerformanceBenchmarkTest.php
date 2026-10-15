<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PerformanceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_homepage_and_catalog_response_speed(): void
    {
        Cache::flush();

        // 1. Initial Call (Cache Warming)
        $warmRes = $this->getJson('/api/client/homepage');
        $warmRes->assertStatus(200);

        // 2. Measure Cached Homepage API
        $startTime = microtime(true);
        $res = $this->getJson('/api/client/homepage');
        $durationMs = (microtime(true) - $startTime) * 1000;

        $res->assertStatus(200);
        $this->assertNotEmpty($res->json('data.categories'), 'Categories should not be empty');
        $this->assertNotEmpty($res->json('data.latest'), 'Latest products should not be empty');
        $this->assertNotEmpty($res->json('data.best_seller'), 'Best seller products should not be empty');
        $this->assertNotEmpty($res->json('data.sliders'), 'Hero sliders should not be empty');

        echo "\n---------------------------------------------------------";
        echo "\n[BENCHMARK] Homepage API execution time: " . round($durationMs, 2) . " ms";

        // 3. Measure Catalog API
        $startTime = microtime(true);
        $catRes = $this->getJson('/api/client/products');
        $catDurationMs = (microtime(true) - $startTime) * 1000;

        $catRes->assertStatus(200);
        $this->assertNotEmpty($catRes->json('data'), 'Catalog products should not be empty');
        echo "\n[BENCHMARK] Catalog API execution time: " . round($catDurationMs, 2) . " ms";

        // 4. Measure Category Details API
        $categorySlug = $res->json('data.categories.0.slug');
        $this->assertNotNull($categorySlug);

        $startTime = microtime(true);
        $catDetailRes = $this->getJson("/api/client/category-details/{$categorySlug}");
        $catDetailDurationMs = (microtime(true) - $startTime) * 1000;

        $catDetailRes->assertStatus(200)
                     ->assertJsonPath('data.slug', $categorySlug);

        echo "\n[BENCHMARK] Category Details API execution time: " . round($catDetailDurationMs, 2) . " ms";
        echo "\n---------------------------------------------------------\n";
    }
}
