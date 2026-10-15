<?php

namespace App\Http\Controllers\API\Frontend;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Frontend\ProductResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected array $productRelations = [
        'media',
        'price',
        'prices.size',
        'sizes',
        'colors',
        'categories',
        'stocks',
        'colorSet',
    ];

    /**
     * Complete filtered product catalog.
     */
    public function catalog(Request $request): JsonResponse
    {
        $query = Product::with($this->productRelations)->where('is_active', true);

        // Filter by Category
        if ($request->filled('category')) {
            $categorySlug = $request->category;
            $query->whereHas('categories', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug)->orWhere('categories.id', $categorySlug);
            });
        }

        // Filter by Cloth Type (1: Kurti+Pant, 2: Kameez, 3: Salwar, 4: Universal/Saree/Lehenga)
        if ($request->filled('cloth_type')) {
            $clothTypes = is_array($request->cloth_type) ? $request->cloth_type : explode(',', $request->cloth_type);
            $query->whereIn('cloth_type', $clothTypes);
        }

        // Filter by Size
        if ($request->filled('size')) {
            $sizes = is_array($request->size) ? $request->size : explode(',', $request->size);
            $query->whereHas('sizes', function ($q) use ($sizes) {
                $q->whereIn('standard', $sizes)->orWhereIn('name', $sizes);
            });
        }

        // Filter by Color
        if ($request->filled('color')) {
            $colors = is_array($request->color) ? $request->color : explode(',', $request->color);
            $query->whereHas('colors', function ($q) use ($colors) {
                $q->whereIn('color_code', $colors)->orWhereIn('name', $colors);
            });
        }

        // Filter by Price Range
        if ($request->filled('min_price')) {
            $query->whereHas('price', function ($q) use ($request) {
                $q->where('price', '>=', (float) $request->min_price);
            });
        }
        if ($request->filled('max_price')) {
            $query->whereHas('price', function ($q) use ($request) {
                $q->where('price', '<=', (float) $request->max_price);
            });
        }

        // Flag filters
        if ($request->boolean('new_arrival')) {
            $query->where('new_arrival', true);
        }
        if ($request->boolean('best_seller')) {
            $query->where('best_seller', true);
        }
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('fabric_type', 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->join('prices', 'products.id', '=', 'prices.product_id')
                      ->select('products.*')
                      ->orderBy('prices.price', 'asc');
                break;
            case 'price_high':
                $query->join('prices', 'products.id', '=', 'prices.product_id')
                      ->select('products.*')
                      ->orderBy('prices.price', 'desc');
                break;
            case 'popular':
                $query->orderBy('best_seller', 'desc')->latest();
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $perPage = (int) $request->get('per_page', 16);
        $paginated = $query->paginate($perPage);

        return ApiResponse::success(
            ProductResource::collection($paginated->items()),
            'Catalog fetched successfully',
            200,
            [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ]
        );
    }

    /**
     * Single product details by slug.
     */
    public function getProduct(string $slug): JsonResponse
    {
        $product = Product::with($this->productRelations)
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->first();

        if (! $product) {
            return ApiResponse::notFound('Product not found');
        }

        return ApiResponse::success(new ProductResource($product));
    }

    /**
     * Live search endpoint.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return ApiResponse::success([]);
        }

        $products = Product::with($this->productRelations)
            ->where('is_active', true)
            ->where('name', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();

        return ApiResponse::success(ProductResource::collection($products));
    }

    /**
     * Best selling products.
     */
    public function getBestSellingProducts(): JsonResponse
    {
        $products = Product::with($this->productRelations)
            ->where('is_active', true)
            ->where('best_seller', true)
            ->latest()
            ->limit(12)
            ->get();

        return ApiResponse::success(ProductResource::collection($products));
    }

    /**
     * Unstitched collection.
     */
    public function getUnstitchedProducts(): JsonResponse
    {
        $products = Product::with($this->productRelations)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('cloth_type', 4)
                  ->orWhereHas('categories', function ($cat) {
                      $cat->where('name', 'LIKE', '%Unstitched%');
                  });
            })
            ->latest()
            ->limit(16)
            ->get();

        return ApiResponse::success(ProductResource::collection($products));
    }

    /**
     * Related products by category.
     */
    public function related(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)->orWhere('id', $slug)->first();

        if (! $product) {
            return ApiResponse::success([]);
        }

        $categoryIds = $product->categories()->pluck('categories.id')->toArray();

        $related = Product::with($this->productRelations)
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            })
            ->limit(8)
            ->get();

        return ApiResponse::success(ProductResource::collection($related));
    }

    /**
     * Blog posts list.
     */
    public function posts(): JsonResponse
    {
        $posts = Post::where('is_published', true)->latest()->paginate(10);
        return ApiResponse::success($posts->items(), 'Posts fetched successfully', 200, [
            'current_page' => $posts->currentPage(),
            'last_page'    => $posts->lastPage(),
            'total'        => $posts->total(),
        ]);
    }

    /**
     * Single blog post.
     */
    public function post(string $slug): JsonResponse
    {
        $post = Post::where('slug', $slug)->orWhere('id', $slug)->first();

        if (! $post) {
            return ApiResponse::notFound('Post not found');
        }

        return ApiResponse::success($post);
    }
}
