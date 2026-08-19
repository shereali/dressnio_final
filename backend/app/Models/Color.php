<?php

namespace App\Models;

use App\Traits\UserAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Color extends Model
{
    use HasFactory, UserAction, SoftDeletes;

    protected $guarded = [];

    public function products()
    {
        return $this->morphToMany(Product::class, 'productable');
    }

    public function colorSet()
    {
        return $this->belongsTo(ColorSet::class);
    }
}
