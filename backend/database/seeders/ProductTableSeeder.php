<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Color;
use App\Models\Media;
use App\Models\Mediable;
use App\Models\Price;
use App\Models\Product;
use App\Models\Productable;
use App\Models\Size;
use App\Models\Stock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductTableSeeder extends Seeder
{
    /**
     * Seed realistic demo products with prices/stocks (idempotent by slug).
     */
    public function run(): void
    {
        $categories = Category::all()->keyBy('name');
        $colors = Color::all();
        $sizesKameez = Size::where('size_type', 1)->get();
        $sizesSalwar = Size::where('size_type', 2)->get();

        $demoImages = [
            '/brand/hero-chic.jpg',
            '/brand/hero-evening.jpg',
            '/brand/hero-summer.jpg',
            '/brand/hero-festive.jpg',
            '/brand/story-1.jpg',
            '/brand/story-2.jpg',
            '/brand/story-3.jpg',
            '/brand/story-4.jpg',
        ];

        $products = [
            ['name' => 'Midnight Blue Embroidery Kurta Set', 'category' => 'Kurta Sets', 'cloth_type' => 1, 'base' => 3450],
            ['name' => 'Pastel Pink Silk Salwar Kameez', 'category' => 'Suit Sets', 'cloth_type' => 1, 'base' => 4200],
            ['name' => 'Floral Print Chiffon Maxi', 'category' => 'Maxis', 'cloth_type' => 4, 'base' => 2800],
            ['name' => 'Bridal Crimson Lehenga Choli', 'category' => 'Lehengas', 'cloth_type' => 4, 'base' => 12500],
            ['name' => 'Boho Style Linen Dress', 'category' => 'Dresses', 'cloth_type' => 4, 'base' => 3100],
            ['name' => 'Electric Teal Co-ord Set', 'category' => 'Tops & Co-ord Sets', 'cloth_type' => 4, 'base' => 2650],
            ['name' => 'Hand-woven Jamdani Saree', 'category' => 'Sarees', 'cloth_type' => 4, 'base' => 6800],
            ['name' => 'Pure Soft Silk Raw Fabric', 'category' => 'Raw Fabrics', 'cloth_type' => 4, 'base' => 1900],
            ['name' => 'Unstitched Festive Suit Piece', 'category' => 'Unstitched Suits', 'cloth_type' => 1, 'base' => 2400],
            ['name' => 'Pure Cotton Daily Wear Dupatta', 'category' => 'Dupattas', 'cloth_type' => 4, 'base' => 850],
            ['name' => 'Mint Green Georgette Kameez', 'category' => 'Kurta Sets', 'cloth_type' => 2, 'base' => 2950],
            ['name' => 'Designer Black Salwar', 'category' => 'Suit Sets', 'cloth_type' => 3, 'base' => 3300],
            ['name' => 'Vibrant Orange Anarkali Suit', 'category' => 'Suit Sets', 'cloth_type' => 1, 'base' => 5200],
            ['name' => 'Mauve Lavender Party Dress', 'category' => 'Dresses', 'cloth_type' => 4, 'base' => 3850],
            ['name' => 'Golden Shimmer Lehenga', 'category' => 'Lehengas', 'cloth_type' => 4, 'base' => 9800],
            ['name' => 'Casual White Cotton Tops', 'category' => 'Tops & Co-ord Sets', 'cloth_type' => 4, 'base' => 1350],
            ['name' => 'Elegant Saffron Silk Saree', 'category' => 'Sarees', 'cloth_type' => 4, 'base' => 7200],
            ['name' => 'Turquoise Blue Maxi Gown', 'category' => 'Maxis', 'cloth_type' => 4, 'base' => 3600],
            ['name' => 'Traditional Phulkari Dupatta', 'category' => 'Dupattas', 'cloth_type' => 4, 'base' => 950],
            ['name' => 'Royal Purple Velvet Suit Set', 'category' => 'Suit Sets', 'cloth_type' => 1, 'base' => 5600],
            ['name' => 'Peach Fuzz Cotton Kameez', 'category' => 'Kurta Sets', 'cloth_type' => 2, 'base' => 2150],
            ['name' => 'Navy Blue Stitched Salwar', 'category' => 'Suit Sets', 'cloth_type' => 3, 'base' => 3100],
            ['name' => 'Summer Breeze Floral Suit', 'category' => 'Unstitched Suit Sets', 'cloth_type' => 1, 'base' => 2900],
            ['name' => 'Classic Black Raw Fabric 5m', 'category' => 'Raw Fabrics', 'cloth_type' => 4, 'base' => 1650],
            ['name' => 'Premium Multi-color Lehnga Choli', 'category' => 'Lehengas', 'cloth_type' => 4, 'base' => 11500],
        ];

        $flagMap = [
            'new_arrival'      => [0, 2, 4, 6, 8, 10, 12, 14, 16, 18, 20, 22, 24],
            'best_seller'      => [0, 5, 10, 15, 20],
            'fashionist_choice'=> [0, 4, 8, 12, 16, 20, 24],
            'highlight'        => [0, 10, 20],
            'is_featured'      => [0, 3, 6, 9, 12, 15, 18, 21, 24],
        ];

        foreach ($products as $index => $item) {
            $slug = Str::slug($item['name']);
            $product = Product::firstOrCreate(
                ['slug' => $slug],
                [
                    'name'        => $item['name'],
                    'description' => "Experience the ultimate comfort and style with our {$item['name']}. Crafted from premium fabrics with exquisite detailing, this piece is perfect for festive occasions, daily wear and everything in between. Pair it with statement jewellery and your favourite footwear for a complete look.",
                    'is_active'           => true,
                    'is_featured'         => in_array($index, $flagMap['is_featured']),
                    'new_arrival'         => in_array($index, $flagMap['new_arrival']),
                    'best_seller'         => in_array($index, $flagMap['best_seller']),
                    'fashionist_choice'   => in_array($index, $flagMap['fashionist_choice']),
                    'highlight'           => in_array($index, $flagMap['highlight']),
                    'is_size_visible'     => true,
                    'is_color_visible'    => true,
                    'has_color_variation' => true,
                    'stock_status'        => 1,
                    'fabric_type'         => ($index % 5 == 0) ? 2 : 1,
                    'cloth_type'          => $item['cloth_type'],
                    'stock_available'     => 0,
                    'created_by'          => 1,
                ]
            );

            // Ensure Product has a Media image
            $imgUrl = $demoImages[$index % count($demoImages)];
            $media = Media::firstOrCreate(
                ['slug' => 'prod-media-' . $slug],
                [
                    'name'        => $item['name'],
                    'media_url'   => $imgUrl,
                    'category_id' => 1,
                    'media_type'  => 1,
                ]
            );

            if (! Mediable::where('media_id', $media->id)->where('mediable_id', $product->id)->where('mediable_type', 'products')->exists()) {
                Mediable::create([
                    'media_id'      => $media->id,
                    'mediable_id'   => $product->id,
                    'mediable_type' => 'products',
                ]);
            }

            $category = $categories->get($item['category']);
            if ($category && ! $product->categories()->where('categories.id', $category->id)->exists()) {
                Productable::create([
                    'product_id'       => $product->id,
                    'productable_id'   => $category->id,
                    'productable_type' => 'categories',
                ]);
            }

            $selectedColor = $colors->random();
            if (! $product->colors()->where('colors.id', $selectedColor->id)->exists()) {
                Productable::create([
                    'product_id'       => $product->id,
                    'productable_id'   => $selectedColor->id,
                    'productable_type' => 'colors',
                ]);
            }

            $targetSizes = ($item['cloth_type'] == 3) ? $sizesSalwar : $sizesKameez;
            foreach ($targetSizes as $size) {
                $priceExists = Price::where('product_id', $product->id)->where('size_id', $size->id)->exists();
                if ($priceExists) {
                    continue;
                }

                $basePrice = $item['base'] + ($size->standard === 'XXL' ? 400 : ($size->standard === 'XS' ? -300 : 0));
                $discount = in_array($index, [5, 11, 17]) ? 15 : 0;

                $price = Price::create([
                    'product_id' => $product->id,
                    'size_id'    => $size->id,
                    'price'      => $basePrice,
                    'discount'   => $discount,
                    'created_by' => 1,
                ]);

                $stock = Stock::create([
                    'product_id' => $product->id,
                    'size_id'    => $size->id,
                    'price_id'   => $price->id,
                    'quantity'   => 10 + $index % 20,
                    'stock_in'   => 1,
                    'created_by' => 1,
                ]);

                Productable::create([
                    'product_id'       => $product->id,
                    'productable_id'   => $price->id,
                    'productable_type' => 'prices',
                ]);
                Productable::create([
                    'product_id'       => $product->id,
                    'productable_id'   => $stock->id,
                    'productable_type' => 'stocks',
                ]);

                if ($item['cloth_type'] != 4) {
                    Productable::firstOrCreate([
                        'product_id'       => $product->id,
                        'productable_id'   => $size->id,
                        'productable_type' => 'sizes',
                    ]);
                }
            }

            $totalStock = Stock::where('product_id', $product->id)->sum('quantity');
            $product->update(['stock_available' => $totalStock]);
        }

        $this->command?->info(count($products) . ' products ensured');
    }
}
