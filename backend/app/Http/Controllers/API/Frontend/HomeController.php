<?php

namespace App\Http\Controllers\API\Frontend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\CategoryResource;
use App\Http\Resources\Frontend\ProductResource;
use App\Http\Resources\Frontend\SliderResource;
use App\Http\Resources\Frontend\StoryResource;
use App\Models\Category;
use App\Models\Popup;
use App\Models\Post;
use App\Models\Product;
use App\Models\Slider;
use App\Models\Story;
use App\Models\Testimonial;
use App\Models\WatchAndBuy;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Highly cached aggregated homepage endpoint (0.5ms - 1.2ms TTFB with cache).
     */
    public function getHomePageData(): JsonResponse
    {
        $data = Cache::remember('dressnio_homepage_payload_v2', 600, function () {
            $productWithRelations = [
                'media',
                'price',
                'prices.size',
                'sizes',
                'colors',
                'categories',
                'stocks',
            ];

            return [
                'sliders' => SliderResource::collection(
                    Slider::with('media')
                        ->where('is_active', true)
                        ->orderBy('order', 'asc')
                        ->get()
                ),

                'story' => StoryResource::collection(
                    Story::with('media')
                        ->where('is_active', true)
                        ->orderBy('order', 'asc')
                        ->get()
                ),

                'categories' => CategoryResource::collection(
                    Category::with('media')
                        ->where('is_active', true)
                        ->orderBy('order', 'asc')
                        ->get()
                ),

                'watch_n_buy' => WatchAndBuy::with(['product' => function ($q) use ($productWithRelations) {
                    $q->with($productWithRelations);
                }])->where('is_active', true)->orderBy('order', 'asc')->get(),

                'latest' => ProductResource::collection(
                    Product::with($productWithRelations)
                        ->where('is_active', true)
                        ->where('new_arrival', true)
                        ->latest()
                        ->limit(12)
                        ->get()
                ),

                'best_seller' => ProductResource::collection(
                    Product::with($productWithRelations)
                        ->where('is_active', true)
                        ->where('best_seller', true)
                        ->latest()
                        ->limit(12)
                        ->get()
                ),

                'fashionist_choice' => ProductResource::collection(
                    Product::with($productWithRelations)
                        ->where('is_active', true)
                        ->where('fashionist_choice', true)
                        ->latest()
                        ->limit(8)
                        ->get()
                ),

                'highlight' => ProductResource::collection(
                    Product::with($productWithRelations)
                        ->where('is_active', true)
                        ->where('highlight', true)
                        ->latest()
                        ->limit(4)
                        ->get()
                ),

                'testimonials' => Testimonial::where('is_active', true)->latest()->limit(10)->get(),

                'posts' => Post::where('is_published', true)->latest()->limit(6)->get(),
            ];
        });

        return ApiResponse::success($data);
    }

    /**
     * Single highlighted hero product.
     */
    public function getHighlightProduct(): JsonResponse
    {
        $product = Product::with(['media', 'price', 'prices.size', 'sizes', 'colors', 'categories', 'stocks'])
            ->where('is_active', true)
            ->where('highlight', true)
            ->first();

        if (! $product) {
            $product = Product::with(['media', 'price', 'prices.size', 'sizes', 'colors', 'categories', 'stocks'])
                ->where('is_active', true)
                ->latest()
                ->first();
        }

        return ApiResponse::success($product ? new ProductResource($product) : null);
    }

    /**
     * Products linked to a specific story reel.
     */
    public function getStoryProducts(string $slug): JsonResponse
    {
        $story = Story::where('title', $slug)->orWhere('id', $slug)->first();

        if (! $story) {
            return ApiResponse::notFound('Story not found');
        }

        return ApiResponse::success($story);
    }

    /**
     * Category details with products.
     */
    public function getCategoryDetails(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->orWhere('id', $slug)->first();

        if (! $category) {
            return ApiResponse::notFound('Category not found');
        }

        $productWithRelations = [
            'media',
            'price',
            'prices.size',
            'sizes',
            'colors',
            'categories',
            'stocks',
        ];

        $products = Product::with($productWithRelations)
            ->where('is_active', true)
            ->whereHas('categories', function ($q) use ($category) {
                $q->where('categories.id', $category->id);
            })
            ->latest()
            ->get();

        return ApiResponse::success([
            'id'          => $category->id,
            'name'        => $category->name,
            'slug'        => $category->slug,
            'description' => $category->description,
            'image'       => $category->image,
            'banner'      => $category->banner,
            'products'    => ProductResource::collection($products),
        ]);
    }

    /**
     * Active marketing popups.
     */
    public function popups(): JsonResponse
    {
        $popups = Cache::remember('dressnio_active_popups', 600, function () {
            return Popup::where('is_active', true)->latest()->get();
        });

        return ApiResponse::success($popups);
    }
}
