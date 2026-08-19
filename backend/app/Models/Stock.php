<?php

namespace App\Models;

use App\Traits\UserAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory, UserAction, SoftDeletes;

    protected $guarded = [];

    public function products()
    {
        return $this->morphToMany(Product::class, 'productable');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function price()
    {
        return $this->belongsTo(Price::class);
    }
}
