<?php

namespace App\Models;

use App\Traits\UserAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes, UserAction;

    protected $guarded = [];

    protected $casts = [
        'is_active'           => 'boolean',
        'is_featured'         => 'boolean',
        'new_arrival'         => 'boolean',
        'best_seller'         => 'boolean',
        'highlight'           => 'boolean',
        'fashionist_choice'   => 'boolean',
        'has_color_variation' => 'boolean',
        'is_size_visible'     => 'boolean',
        'is_color_visible'    => 'boolean',
    ];


    public function colorSet()
    {
        return $this->belongsTo(ColorSet::class);
    }



    public function media()
    {
        return $this->morphToMany(Media::class, 'mediable');
    }


    public function gallery()
    {
        return $this->morphToMany(Media::class, 'mediable');
    }

    public function price()
    {
        return $this->hasOne(Price::class);
    }

    /**
     * Get all of the sizes that are assigned this product.
     */
    public function prices()
    {
        return $this->morphedByMany(Price::class, 'productable');
    }
    /**
     * Get all of the sizes that are assigned this product.
     */
    public function sizes()
    {
        return $this->morphedByMany(Size::class, 'productable');
    }

    /**
     * Get all of the colors that are assigned this product.
     */
    public function colors()
    {
        return $this->morphedByMany(Color::class, 'productable');
    }
    /**
     * Get all of the stocks that are assigned this product.
     */
    public function stocks()
    {
        return $this->morphedByMany(Stock::class, 'productable');
    }
    /**
     * Get all of the categories that are assigned this product.
     */
    public function categories()
    {
        return $this->morphedByMany(Category::class, 'productable');
    }

    /**
     * Get all of the categories that are assigned this product.
     */

    public function purchases()
    {
        return $this->morphedByMany(Purchase::class, 'productable');
    }

    /**
     * Get all of the categories that are assigned this product.
     */

    public function sales()
    {
        return $this->morphedByMany(Sale::class, 'productable');
    }
}
