<?php

namespace App\Models;

use App\Traits\UserAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Price extends Model
{
    use HasFactory, UserAction, SoftDeletes;
    protected $guarded = [];

    /**
     * Get all of the products that are assigned this size.
     */

    public function products()
    {
        return $this->morphToMany(Product::class, 'productable');
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }
}
