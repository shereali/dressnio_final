<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\ColorSet;
use App\Models\Size;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GeneralTableSeeder extends Seeder
{
    /**
     * Seed sizes, color sets and colors (idempotent).
     */
    public function run(): void
    {
        $sizes = [
            // Kameez/Shirt sizes (size_type = 1)
            ['size_type' => 1, 'standard' => 'XS', 'bust' => 34, 'lengths' => 45],
            ['size_type' => 1, 'standard' => 'S', 'bust' => 36, 'lengths' => 45],
            ['size_type' => 1, 'standard' => 'M', 'bust' => 38, 'lengths' => 46],
            ['size_type' => 1, 'standard' => 'L', 'bust' => 40, 'lengths' => 46],
            ['size_type' => 1, 'standard' => 'XL', 'bust' => 42, 'lengths' => 47],
            ['size_type' => 1, 'standard' => 'XXL', 'bust' => 44, 'lengths' => 47],

            // Salwar/Pant sizes (size_type = 2)
            ['size_type' => 2, 'standard' => '36', 'waist' => 36, 'hip' => 40, 'lengths' => 38],
            ['size_type' => 2, 'standard' => '38', 'waist' => 38, 'hip' => 42, 'lengths' => 39],
            ['size_type' => 2, 'standard' => '40', 'waist' => 40, 'hip' => 44, 'lengths' => 40],
            ['size_type' => 2, 'standard' => '42', 'waist' => 42, 'hip' => 46, 'lengths' => 41],
            ['size_type' => 2, 'standard' => '44', 'waist' => 44, 'hip' => 48, 'lengths' => 42],
        ];

        foreach ($sizes as $size) {
            Size::firstOrCreate(
                ['standard' => $size['standard'], 'size_type' => $size['size_type']],
                $size + ['created_by' => 1]
            );
        }

        $colorSets = [
            ['name' => 'Classic Colors', 'slug' => 'classic-colors'],
            ['name' => 'Pastel Collection', 'slug' => 'pastel-collection'],
            ['name' => 'Vibrant Party Wear', 'slug' => 'vibrant-party-wear'],
        ];

        $colorSetsById = [];
        foreach ($colorSets as $set) {
            $model = ColorSet::firstOrCreate(
                ['slug' => $set['slug']],
                $set + ['created_by' => 1]
            );
            $colorSetsById[$set['name']] = $model->id;
        }

        $colors = [
            // Classic
            ['color_name' => 'Midnight Black', 'color_code' => '#000000', 'set' => 'Classic Colors'],
            ['color_name' => 'Pearl White', 'color_code' => '#FFFFFF', 'set' => 'Classic Colors'],
            ['color_name' => 'Navy Blue', 'color_code' => '#1B2A4A', 'set' => 'Classic Colors'],
            ['color_name' => 'Royal Red', 'color_code' => '#C8102E', 'set' => 'Classic Colors'],

            // Pastel
            ['color_name' => 'Mint Green', 'color_code' => '#98FF98', 'set' => 'Pastel Collection'],
            ['color_name' => 'Peach Fuzz', 'color_code' => '#FFBE98', 'set' => 'Pastel Collection'],
            ['color_name' => 'Sky Blue', 'color_code' => '#87CEEB', 'set' => 'Pastel Collection'],
            ['color_name' => 'Lavender', 'color_code' => '#E6E6FA', 'set' => 'Pastel Collection'],

            // Vibrant
            ['color_name' => 'Magenta Pink', 'color_code' => '#D104FB', 'set' => 'Vibrant Party Wear'],
            ['color_name' => 'Electric Orange', 'color_code' => '#FF4500', 'set' => 'Vibrant Party Wear'],
            ['color_name' => 'Deep Teal', 'color_code' => '#008080', 'set' => 'Vibrant Party Wear'],
            ['color_name' => 'Gold', 'color_code' => '#D4A94E', 'set' => 'Vibrant Party Wear'],
        ];

        foreach ($colors as $color) {
            Color::firstOrCreate(
                ['color_name' => $color['color_name'], 'color_set_id' => $colorSetsById[$color['set']]],
                [
                    'color_code'   => $color['color_code'],
                    'color_set_id' => $colorSetsById[$color['set']],
                    'created_by'   => 1,
                ]
            );
        }

        $this->command->info('Sizes, color sets and colors ensured');
    }
}
