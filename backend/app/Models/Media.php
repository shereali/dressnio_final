<?php

namespace App\Models;

use App\Traits\UserAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory, UserAction, SoftDeletes;
    protected $guarded = [];

    /**
     * Get all of the posts that are assigned this tag.
     */
    public function products()
    {
        return $this->morphedByMany(Product::class, 'mediable');
    }

    public function color()
    {
        return $this->hasOne(Color::class);
    }

    /**
     * Get all of the videos that are assigned this tag.
     */
    // public function videos()
    // {
    //     return $this->morphedByMany(Video::class, 'mediable');
    // }
}
