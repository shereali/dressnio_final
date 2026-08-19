<?php

namespace App\Models;

use App\Traits\UserAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Size extends Model
{
    use HasFactory, UserAction, SoftDeletes;
    protected $guarded = [];

    public function prices()
    {
        return $this->hasOne(Price::class);
    }

    /**
     * Get all of the products that are assigned this size.
     */

    public function products()
    {
        return $this->morphToMany(Product::class, 'productable');
    }
}
