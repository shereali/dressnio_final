<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Sale;
use App\Models\Size;
use App\Models\Color;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Category;
use App\Models\Price;
use App\Models\Purchase;
use App\Models\Slider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Relation::morphMap([
            'products'   => Product::class,
            'posts'      => Post::class,
            'prices'     => Price::class,
            'colors'     => Color::class,
            'sizes'      => Size::class,
            'stocks'     => Stock::class,
            'categories' => Category::class,
            'purchases'  => Purchase::class,
            'sales'      => Sale::class,
        ]);
    }
}
