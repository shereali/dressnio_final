<?php

namespace App\Http\Controllers\API\Backend;

use App\Helpers\ApiResponse;
use App\Models\Price;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Mediable;
use App\Models\OldPrice;
use App\Models\OldStock;
use App\Models\Productable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\CrudRepository;
use App\Http\Resources\Backend\ProductResource;
use App\Http\Requests\Backend\ProductRequest;

class ProductController extends Controller
{
    public CrudRepository $repository;

    public function __construct(CrudRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(Product::class);
        $this->repository->setResource(ProductResource::class);
        $this->repository->message->setMessage('Product');
    }

    public function index()
    {
        $this->repository->setWith(['media', 'price', 'prices.size', 'sizes', 'colors', 'categories', 'stocks']);

        return $this->repository->fetchData();
    }

    /**
     * Create or update a product with its media, colors, categories, prices and stocks,
     * all inside a database transaction.
     */
    public function processingStore(ProductRequest $request, ?int $id = null)
    {
        return DB::transaction(function () use ($request, $id) {
            $data = [
                'name'                => $request->name,
                'description'         => $request->description,
                'slug'                => $request->slug ?: Str::slug($request->name),
                'is_active'           => $request->boolean('is_active'),
                'is_featured'         => $request->boolean('is_featured'),
                'new_arrival'         => $request->boolean('new_arrival'),
                'best_seller'         => $request->boolean('best_seller'),
                'highlight'           => $request->boolean('highlight'),
                'fashionist_choice'   => $request->boolean('fashionist_choice'),
                'has_color_variation' => $request->boolean('has_color_variation'),
                'color_set_id'        => $request->color_set_id,
                'is_size_visible'     => $request->boolean('is_size_visible'),
                'is_color_visible'    => $request->boolean('is_color_visible'),
                'cloth_type'          => $request->cloth_type,
                'fabric_type'         => $request->fabric_type,
                'stock_status'        => $request->stock_status,
            ];

            if ($id === null) {
                $product = Product::create($data);
            } else {
                $product = Product::find($id);

                if (! $product) {
                    return ApiResponse::notFound('Product not found');
                }

                $product->update($data);

                // Clean up all pivot/child rows so they can be re-created below.
                Mediable::where('mediable_id', $product->id)->where('mediable_type', 'products')->delete();
                Productable::where('product_id', $product->id)->delete();
                Price::where('product_id', $product->id)->forceDelete();
                Stock::where('product_id', $product->id)->forceDelete();
                OldPrice::where('product_id', $product->id)->forceDelete();
                OldStock::where('product_id', $product->id)->forceDelete();
            }

            // Attach media
            foreach ((array) $request->media_id as $mediaId) {
                if ($mediaId) {
                    Mediable::create([
                        'media_id'      => $mediaId,
                        'mediable_id'   => $product->id,
                        'mediable_type' => 'products',
                    ]);
                }
            }

            // Attach colors
            foreach ((array) $request->color_id as $colorId) {
                if ($colorId) {
                    Productable::firstOrCreate([
                        'product_id'       => $product->id,
                        'productable_id'   => $colorId,
                        'productable_type' => 'colors',
                    ]);
                }
            }

            // Attach categories
            foreach ((array) $request->category_id as $categoryId) {
                if ($categoryId) {
                    Productable::firstOrCreate([
                        'product_id'       => $product->id,
                        'productable_id'   => $categoryId,
                        'productable_type' => 'categories',
                    ]);
                }
            }

            // Prices, sizes and stocks
            if (is_array($request->price)) {
                foreach ($request->price as $key => $priceVal) {
                    $size = null;

                    if (in_array((int) $request->cloth_type, [1, 2])) {
                        $size = $request->kameez_size_id[$key] ?? null;
                    } elseif ((int) $request->cloth_type === 3) {
                        $size = $request->salwar_size_id[$key] ?? null;
                    }

                    $price = Price::create([
                        'product_id' => $product->id,
                        'size_id'    => $size,
                        'price'      => (float) $priceVal,
                        'discount'   => $request->discount[$key] ?? 0,
                    ]);

                    if ($id === null) {
                        OldPrice::create([
                            'product_id' => $product->id,
                            'size_id'    => $size,
                            'price'      => (float) $priceVal,
                            'discount'   => $request->discount[$key] ?? 0,
                        ]);
                    }

                    if ((int) $request->cloth_type !== 4) {
                        Productable::firstOrCreate([
                            'product_id'       => $product->id,
                            'productable_id'   => $price->id,
                            'productable_type' => 'prices',
                        ]);

                        if ($size) {
                            Productable::firstOrCreate([
                                'product_id'       => $product->id,
                                'productable_id'   => $size,
                                'productable_type' => 'sizes',
                            ]);
                        }
                    }

                    $stock = Stock::create([
                        'product_id' => $product->id,
                        'size_id'    => $size,
                        'price_id'   => $price->id,
                        'quantity'   => $request->quantity[$key] ?? 0,
                        'stock_in'   => $request->stock_status,
                    ]);

                    if ($id === null) {
                        OldStock::create([
                            'product_id' => $product->id,
                            'size_id'    => $size,
                            'price_id'   => $price->id,
                            'quantity'   => $request->quantity[$key] ?? 0,
                            'stock_in'   => $request->stock_status,
                        ]);
                    }

                    Productable::firstOrCreate([
                        'product_id'       => $product->id,
                        'productable_id'   => $stock->id,
                        'productable_type' => 'stocks',
                    ]);

                    // For cloth_type 1 (kurti + pant), also link the pant size.
                    if ((int) $request->cloth_type === 1 && isset($request->salwar_size_id[$key])) {
                        Productable::firstOrCreate([
                            'product_id'       => $product->id,
                            'productable_id'   => $request->salwar_size_id[$key],
                            'productable_type' => 'sizes',
                        ]);
                    }
                }
            }

            $product->update([
                'stock_available' => Stock::where('product_id', $product->id)->sum('quantity'),
            ]);

            return ApiResponse::success(
                new ProductResource($product->load(['media', 'price', 'prices.size', 'sizes', 'colors', 'categories', 'stocks'])),
                $id === null ? 'Product created successfully' : 'Product updated successfully',
                $id === null ? 201 : 200
            );
        });
    }

    public function store(ProductRequest $request)
    {
        return $this->processingStore($request);
    }

    public function show(string $id)
    {
        $product = Product::with(['media', 'price', 'prices.size', 'sizes', 'colors', 'categories', 'stocks'])
            ->find((int) $id);

        if (! $product) {
            return ApiResponse::notFound('Product not found');
        }

        return ApiResponse::success(new ProductResource($product));
    }

    public function update(ProductRequest $request, string $id)
    {
        return $this->processingStore($request, (int) $id);
    }

    public function destroy(string $id)
    {
        $product = Product::find((int) $id);

        if (! $product) {
            return ApiResponse::notFound('Product not found');
        }

        $product->delete();

        return ApiResponse::success(null, 'Product deleted successfully');
    }
}
