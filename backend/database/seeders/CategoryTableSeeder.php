<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryTableSeeder extends Seeder
{
    /**
     * Seed the catalog categories (idempotent by slug).
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Kurta Sets', 'image' => '/img/category-kurta.jpg'],
            ['name' => 'Shrug Sets', 'image' => '/img/category-shrug.jpg'],
            ['name' => 'Maxis', 'image' => '/img/category-maxi.jpg'],
            ['name' => 'Lehengas', 'image' => '/img/category-lehenga.jpg'],
            ['name' => 'Suit Sets', 'image' => '/img/category-suit.jpg'],
            ['name' => 'Dresses', 'image' => '/img/category-dress.jpg'],
            ['name' => 'Tops & Co-ord Sets', 'image' => '/img/category-top.jpg'],
            ['name' => 'Sarees', 'image' => '/img/category-saree.jpg'],
            ['name' => 'Raw Fabrics', 'image' => '/img/category-fabric.jpg'],
            ['name' => 'Unstitched Suits', 'image' => '/img/category-unstitched.jpg'],
        ];

        foreach ($categories as $idx => $item) {
            Category::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'name'       => $item['name'],
                    'image'      => $item['image'],
                    'order'      => $idx + 1,
                    'is_active'   => true,
                    'is_featured' => true,
                    'created_by'  => 1,
                ]
            );
        }

        $this->command?->info(count($categories) . ' categories ensured');
    }
}
