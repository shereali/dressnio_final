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
            '/img/p1.jpg',
            '/img/p2.jpg',
            '/img/p3.jpg',
            '/img/p4.jpg',
            '/img/shop1.jpg',
        ];

        // flags: F = featured, N = new arrival, B = best seller, C = fashionist choice, H = highlight
        // [name, category, cloth_type, base price, flags, discount %, short description]
        $products = [
            // Kurta Sets
            ['Midnight Blue Embroidery Kurta Set', 'Kurta Sets', 3, 3450, 'FNBH', 0, 'Hand-embroidered neckline on soft cotton-silk, paired with a straight pant and a printed chiffon dupatta.'],
            ['Mint Green Georgette Kurta Set', 'Kurta Sets', 2, 2950, 'N', 0, 'Flowy georgette kurta with delicate thread work and matching palazzo, ideal for Eid gatherings.'],
            ['Peach Fuzz Cotton Kurta Set', 'Kurta Sets', 2, 2150, 'B', 10, 'Breathable pure cotton set with block-print motifs, made for comfortable everyday wear.'],
            ['Ivory Chikankari Straight Kurta Set', 'Kurta Sets', 3, 4100, 'C', 0, 'Lucknowi chikankari hand-work on ivory mul cotton with a soft cotton dupatta.'],
            ['Mustard Printed Cotton Kurta Set', 'Kurta Sets', 2, 2300, '', 15, 'Bright mustard kurta with ethnic block prints and contrast piping, with a cotton salwar.'],
            // Shrug Sets
            ['Rose Gold Sequin Shrug Set', 'Shrug Sets', 4, 4800, 'FNH', 0, 'Sequin-trimmed open shrug layered over a satin inner and flared bottom for party evenings.'],
            ['Black Lace Overlay Shrug Set', 'Shrug Sets', 4, 3900, 'C', 0, 'Sheer floral lace shrug with a fitted satin top and straight trousers.'],
            ['Dusty Rose Embroidered Shrug Set', 'Shrug Sets', 4, 4350, 'N', 0, 'Thread-embroidered long shrug in dusty rose with a matching inner and palazzo.'],
            ['Sage Green Cotton Shrug Set', 'Shrug Sets', 4, 2750, '', 10, 'Lightweight cotton shrug set in earthy sage, easy to style for daytime outings.'],
            ['Royal Blue Velvet Shrug Set', 'Shrug Sets', 4, 5400, 'B', 0, 'Winter-ready velvet shrug with gold border, worn over a silk inner and tapered pants.'],
            // Maxis
            ['Floral Print Chiffon Maxi', 'Maxis', 4, 2800, 'FNB', 0, 'Full-length chiffon maxi with a romantic floral print, tiered skirt and tie-waist.'],
            ['Turquoise Blue Maxi Gown', 'Maxis', 4, 3600, 'C', 0, 'Floor-sweeping turquoise gown with a flattering A-line cut and flutter sleeves.'],
            ['Terracotta Wrap Maxi Dress', 'Maxis', 4, 3200, 'N', 0, 'Wrap-style terracotta maxi in soft viscose crepe with a defined waist.'],
            ['Emerald Satin Evening Maxi', 'Maxis', 4, 4500, 'H', 0, 'Luxurious emerald satin maxi with a cowl neck and gentle drape for evening events.'],
            ['Polka Dot Summer Maxi', 'Maxis', 4, 2450, 'B', 20, 'Easy-breezy cotton maxi with a polka dot print and smocked bodice.'],
            // Lehengas
            ['Bridal Crimson Lehenga Choli', 'Lehengas', 4, 12500, 'FBH', 0, 'Heavily embroidered crimson bridal lehenga with zardozi work, a matching choli and net dupatta.'],
            ['Golden Shimmer Lehenga', 'Lehengas', 4, 9800, 'N', 0, 'Shimmering gold lehenga with sequin scallop border, ideal for holud and reception.'],
            ['Pastel Pink Net Lehenga Set', 'Lehengas', 4, 8600, 'C', 0, 'Soft pastel pink net lehenga with floral applique and a delicate dupatta.'],
            ['Emerald Green Velvet Lehenga', 'Lehengas', 4, 11200, 'N', 0, 'Rich velvet lehenga in emerald with antique gold embroidery for winter weddings.'],
            ['Premium Multi-colour Lehenga Choli', 'Lehengas', 4, 11500, 'B', 0, 'Vibrant multi-colour lehenga with mirror work and a bandhani dupatta.'],
            // Suit Sets
            ['Pastel Pink Silk Salwar Kameez', 'Suit Sets', 1, 4200, 'FNC', 0, 'Elegant silk salwar kameez in pastel pink with resham embroidery and chiffon dupatta.'],
            ['Vibrant Orange Anarkali Suit', 'Suit Sets', 1, 5200, 'B', 0, 'Floor-length orange anarkali with gota patti detailing and a matching dupatta.'],
            ['Royal Purple Velvet Suit Set', 'Suit Sets', 1, 5600, 'H', 0, 'Plush royal purple velvet suit with golden embroidery, a pant and an organza dupatta.'],
            ['Designer Black Salwar Suit', 'Suit Sets', 3, 3300, 'N', 0, 'Contemporary black suit with silver embellishments and a flared salwar.'],
            ['Navy Blue Stitched Salwar Suit', 'Suit Sets', 3, 3100, '', 15, 'Classic navy suit stitched in a premium cotton blend with a contrast dupatta.'],
            // Dresses
            ['Boho Style Linen Dress', 'Dresses', 4, 3100, 'FNB', 0, 'Relaxed linen midi dress with bohemian embroidery and side pockets.'],
            ['Mauve Lavender Party Dress', 'Dresses', 4, 3850, 'FCH', 0, 'Mauve lavender fit-and-flare dress with a sweetheart neckline, made for parties.'],
            ['Red Satin Wrap Midi Dress', 'Dresses', 4, 3400, 'N', 0, 'Wrap-front midi dress in red satin with puff sleeves and a self-tie belt.'],
            ['Classic Black Cocktail Dress', 'Dresses', 4, 4200, 'C', 0, 'Timeless black cocktail dress with a structured bodice and knee-length skirt.'],
            ['Blue Ruffle Denim Dress', 'Dresses', 4, 2900, 'B', 10, 'Casual denim dress with a ruffled hem and button-down front for weekend wear.'],
            // Tops & Co-ord Sets
            ['Electric Teal Co-ord Set', 'Tops & Co-ord Sets', 4, 2650, 'FN', 0, 'Matching teal crop top and wide-leg pants in a crisp cotton blend.'],
            ['Casual White Cotton Top', 'Tops & Co-ord Sets', 4, 1350, 'B', 0, 'Everyday white cotton top with lace detailing and a relaxed fit.'],
            ['Striped Linen Co-ord Set', 'Tops & Co-ord Sets', 4, 2950, 'N', 0, 'Breathable striped linen shirt and trouser set, perfect for summer days.'],
            ['Pleated Satin Co-ord Set', 'Tops & Co-ord Sets', 4, 3350, 'C', 0, 'Pleated satin blouse with matching skirt in champagne, easy dressy-casual wear.'],
            ['Printed Kimono Top and Pant Set', 'Tops & Co-ord Sets', 4, 2250, '', 20, 'Floral printed kimono top with pull-on trousers for an effortless look.'],
            // Sarees
            ['Hand-woven Jamdani Saree', 'Sarees', 4, 6800, 'FNBH', 0, 'Authentic hand-woven Dhakai jamdani with traditional motifs on fine muslin cotton.'],
            ['Elegant Saffron Silk Saree', 'Sarees', 4, 7200, 'C', 0, 'Pure silk saree in saffron with a woven zari border and rich pallu.'],
            ['Royal Blue Katan Saree', 'Sarees', 4, 8900, 'N', 0, 'Katan silk saree in royal blue with gold zari weaving and a blouse piece.'],
            ['Soft Cotton Tant Saree', 'Sarees', 4, 2200, 'B', 0, 'Lightweight tant cotton saree with a contrast border, great for daily wear.'],
            ['Pastel Georgette Party Saree', 'Sarees', 4, 4600, '', 15, 'Pastel georgette saree with sequin border and a ready-to-stitch blouse piece.'],
            // Raw Fabrics
            ['Pure Soft Silk Raw Fabric', 'Raw Fabrics', 4, 1900, 'FN', 0, 'Pure soft silk, 5 metres, with a smooth finish ideal for custom tailoring.'],
            ['Classic Black Raw Fabric 5m', 'Raw Fabrics', 4, 1650, '', 0, 'Heavyweight black crepe in a 5 metre cut, great for formal tailoring.'],
            ['Premium Cotton Lawn Fabric', 'Raw Fabrics', 4, 1400, 'B', 0, 'Soft cotton lawn in a subtle print, breathable for everyday kameez.'],
            ['Georgette Fabric 4m Ivory', 'Raw Fabrics', 4, 1750, 'N', 0, 'Ivory georgette, 4 metres, with a graceful drape for dupattas and gowns.'],
            ['Embroidered Net Fabric 3m', 'Raw Fabrics', 4, 2100, 'C', 0, 'Embroidered net with scalloped edge, 3 metres, for lehenga and blouse work.'],
            // Unstitched Suits
            ['Unstitched Festive Suit Piece', 'Unstitched Suits', 1, 2400, 'FNB', 0, 'Three-piece festive unstitched set with an embroidered front, plain bottom and dupatta.'],
            ['Summer Breeze Floral Unstitched Suit', 'Unstitched Suits', 1, 2900, 'N', 0, 'Printed lawn unstitched three-piece with a chiffon dupatta.'],
            ['Winter Wool Unstitched Suit', 'Unstitched Suits', 1, 3600, 'C', 0, 'Warm wool-blend suit fabric with a shawl, perfect for winter.'],
            ['Chikankari Cotton Unstitched Suit', 'Unstitched Suits', 1, 3200, 'H', 0, 'Chikankari embroidery on cotton, includes shirt, trouser and dupatta fabrics.'],
            ['Digital Print Silk Unstitched Suit', 'Unstitched Suits', 1, 4100, '', 10, 'Vibrant digital print on silk, with a matching trouser and dupatta.'],
            // Bangladeshi three-piece and salwar kameez
            ['Tangail Cotton Three Piece Set', 'Suit Sets', 1, 3200, 'FNB', 0, 'Bangladeshi three-piece in Tangail cotton with woven border, matching salwar and dupatta.'],
            ['Dhakai Jamdani Three Piece', 'Suit Sets', 1, 6500, 'FNCH', 0, 'Dhakai jamdani-woven kameez with a contrast salwar and soft muslin dupatta, a Bangladeshi festive classic.'],
            ['Rajshahi Silk Salwar Kameez', 'Suit Sets', 1, 5800, 'FC', 0, 'Rajshahi silk salwar kameez with block-print detailing and a coordinated dupatta.'],
            ['Boishakhi Block Print Three Piece', 'Suit Sets', 1, 2750, 'NB', 10, 'Red and white block-print three-piece, made for Pohela Boishakh celebrations.'],
            ['Eid Collection Embroidered Salwar Kameez', 'Suit Sets', 1, 4900, 'FNB', 0, 'Embroidered georgette salwar kameez in Eid colours with a matching dupatta.'],
            ['Khadi Cotton Three Piece (Unstitched)', 'Unstitched Suits', 1, 2600, 'NC', 0, 'Handloom khadi cotton unstitched three-piece with printed dupatta, comfortable for Bangladeshi summers.'],
            ['Dhakai Muslin Salwar Kameez', 'Kurta Sets', 1, 3900, 'F', 0, 'Lightweight muslin salwar kameez with traditional woven motifs and a chiffon dupatta.'],
        ];

        foreach ($products as $index => [$name, $categoryName, $clothType, $basePrice, $flags, $discount, $blurb]) {
            $item = ['name' => $name, 'category' => $categoryName, 'cloth_type' => $clothType, 'base' => $basePrice];
            $slug = Str::slug($item['name']);
            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'        => $item['name'],
                    'description' => $blurb . ' Crafted from premium fabrics with careful finishing, it is perfect for festive occasions and everyday wear alike.',
                    'is_active'           => true,
                    'is_featured'         => str_contains($flags, 'F'),
                    'new_arrival'         => str_contains($flags, 'N'),
                    'best_seller'         => str_contains($flags, 'B'),
                    'fashionist_choice'   => str_contains($flags, 'C'),
                    'highlight'           => str_contains($flags, 'H'),
                    'is_size_visible'     => true,
                    'is_color_visible'    => true,
                    'has_color_variation' => true,
                    'stock_status'        => 1,
                    'fabric_type'         => ($index % 5 == 0) ? 2 : 1,
                    'cloth_type'          => $item['cloth_type'],
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

                $sizePrice = $item['base'] + ($size->standard === 'XXL' ? 400 : ($size->standard === 'XS' ? -300 : 0));

                $price = Price::create([
                    'product_id' => $product->id,
                    'size_id'    => $size->id,
                    'price'      => $sizePrice,
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
