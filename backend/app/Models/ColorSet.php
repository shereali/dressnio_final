<?php

namespace App\Models;

use App\Traits\UserAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ColorSet extends Model
{
    use HasFactory, UserAction, SoftDeletes;
    protected $guarded = [];

    public function colors()
    {
        return $this->hasMany(Color::class);
    }
}
