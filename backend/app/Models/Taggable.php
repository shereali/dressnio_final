<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Taggable extends Model
{
    use HasFactory;


    public static function boot()
    {
        parent::boot();
        Relation::enforceMorphMap([
            'posts' => Post::class,
            'videos' => Video::class,
        ]);
    }
}
