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
            'Kurta Sets',
            'Shrug Sets',
            'Maxis',
            'Lehengas',
            'Suit Sets',
            'Dresses',
            'Tops & Co-ord Sets',
            'Sarees',
            'Raw Fabrics',
            'Unstitched Suits',
            'Dupattas',
            'Unstitched Suit Sets',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name'       => $name,
                    'is_active'  => true,
                    'created_by' => 1,
                ]
            );
        }

        $this->command->info(count($categories) . ' categories ensured');
    }
}
