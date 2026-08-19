<?php

namespace Database\Seeders;

use App\Models\Slider;
use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SliderTableSeeder extends Seeder
{
    /**
     * Seed hero sliders (idempotent). Uses the on-brand hero artwork.
     */
    public function run(): void
    {
        $sliders = [
            [
                'title'    => 'Elegant Evening Collection',
                'subtitle' => 'Sophisticated Designs for Every Occasion',
                'link'     => '/shop',
                'order'    => 1,
                'image'    => '/brand/hero-evening.jpg',
            ],
            [
                'title'    => 'Summer Breeze Edit',
                'subtitle' => 'Light & Colourful Designs for Warm Days',
                'link'     => '/shop',
                'order'    => 2,
                'image'    => '/brand/hero-summer.jpg',
            ],
            [
                'title'    => 'Festive Glamour',
                'subtitle' => 'Dazzling Outfits for Special Celebrations',
                'link'     => '/category/festive',
                'order'    => 3,
                'image'    => '/brand/hero-festive.jpg',
            ],
            [
                'title'    => 'Contemporary Chic',
                'subtitle' => 'Modern Styles for the Fashion-Forward',
                'link'     => '/shop',
                'order'    => 4,
                'image'    => '/brand/hero-chic.jpg',
            ],
        ];

        foreach ($sliders as $slider) {
            $media = Media::firstOrCreate(
                ['slug' => Str::slug('slider-' . $slider['title'])],
                [
                    'name'       => $slider['title'],
                    'media_url'  => $slider['image'],
                    'category_id'=> 1,
                    'media_type' => 1,
                ]
            );

            Slider::firstOrCreate(
                ['title' => $slider['title']],
                [
                    'subtitle'  => $slider['subtitle'],
                    'link'      => $slider['link'],
                    'media_id'  => $media->id,
                    'order'     => $slider['order'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Sliders ensured');
    }
}
