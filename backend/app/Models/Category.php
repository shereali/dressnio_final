<?php

namespace App\Models;

use App\Traits\UserAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, UserAction, SoftDeletes;
    protected $guarded = [];

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
