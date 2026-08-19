<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Story;
use App\Models\Media;
use App\Models\Popup;
use App\Models\Product;
use App\Models\Slider;
use App\Models\Testimonial;
use App\Models\WatchAndBuy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentTableSeeder extends Seeder
{
    /**
     * Seed stories, posts, testimonials, watch-and-buy and popups (idempotent).
     */
    public function run(): void
    {
        $products = Product::limit(8)->get();

        $stories = [
            'New Season Edit'   => '/brand/story-1.jpg',
            'Festive Favourites'=> '/brand/story-2.jpg',
            'Pastel Diaries'    => '/brand/story-3.jpg',
            'Office Chic'       => '/brand/story-4.jpg',
        ];

        foreach ($stories as $name => $image) {
            $media = Media::firstOrCreate(
                ['slug' => Str::slug('story-' . $name)],
                [
                    'name'       => $name,
                    'media_url'  => $image,
                    'category_id'=> 1,
                    'media_type' => 1,
                ]
            );

            $story = Story::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name'      => $name,
                    'media_id'  => $media->id,
                    'is_active' => 1,
                ]
            );

            foreach ($products->take(4) as $product) {
                if (! $story->products()->where('product_id', $product->id)->exists()) {
                    $story->products()->attach($product->id);
                }
            }
        }

        $posts = [
            'The Ultimate Guide to Choosing Your Perfect Kurti' => 'Kurtis are the heart of every woman\'s wardrobe...',
            '5 Styling Tips for Salwar Kameez This Season'      => 'From office wear to festive evenings, the salwar kameez...',
            'Why Handloom Sarees Never Go Out of Style'         => 'There is something timeless about a handloom saree...',
            'Care Guide: Keeping Your Lehenga Wedding-Perfect'   => 'A lehenga is an investment in memories...',
        ];

        foreach ($posts as $title => $excerpt) {
            $media = Media::firstOrCreate(
                ['slug' => Str::slug('post-' . $title)],
                [
                    'name'       => $title,
                    'media_url'  => '/brand/post-' . Str::slug($title) . '.jpg',
                    'category_id'=> 1,
                    'media_type' => 1,
                ]
            );

            Post::firstOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title'       => $title,
                    'media_id'    => $media->id,
                    'is_active'   => true,
                    'description' => '<p>' . $excerpt . '</p><p>Shop the full collection at Dressnio and discover premium fabrics, expert stitching and designs made to celebrate you.</p>',
                ]
            );
        }

        $testimonials = [
            ['name' => 'Rukhsana Akter', 'designation' => 'Dhaka', 'text' => 'The kameez set I ordered fit perfectly and the fabric is gorgeous. Delivery was super fast!'],
            ['name' => 'Tasnim Rahman', 'designation' => 'Chattogram', 'text' => 'Dressnio has the most beautiful sarees. The jamdani I bought got me compliments everywhere.'],
            ['name' => 'Nusrat Jahan', 'designation' => 'Sylhet', 'text' => 'Loved the unstitched suit — the colours were exactly as shown. Will definitely order again.'],
        ];

        foreach ($testimonials as $t) {
            Testimonial::firstOrCreate(
                ['name' => $t['name']],
                [
                    'designation' => $t['designation'],
                    'description' => $t['text'],
                    'is_active'   => true,
                ]
            );
        }

        $watchProduct = $products->first();
        if ($watchProduct) {
            $media = Media::firstOrCreate(
                ['slug' => 'watch-and-buy-intro'],
                [
                    'name'       => 'Watch & Buy Intro',
                    'media_url'  => '/brand/watch-buy.mp4',
                    'category_id'=> 1,
                    'media_type' => 2,
                ]
            );

            WatchAndBuy::firstOrCreate(
                ['name' => 'Watch & Buy Collection'],
                [
                    'slug'       => 'watch-and-buy-collection',
                    'product_id' => $watchProduct->id,
                    'media_id'   => $media->id,
                    'is_active'  => 1,
                ]
            );
        }

        $popupMedia = Media::firstOrCreate(
            ['slug' => 'popup-offer'],
            [
                'name'       => 'Festive Offer',
                'media_url'  => '/brand/popup-offer.jpg',
                'category_id'=> 1,
                'media_type' => 1,
            ]
        );

        Popup::firstOrCreate(
            ['name' => 'Festive Offer Popup'],
            [
                'media_id'   => $popupMedia->id,
                'url'        => '/steal-deals',
                'is_active'  => true,
            ]
        );

        $this->command->info('Stories, posts, testimonials, watch-and-buy and popups ensured');
    }
}
