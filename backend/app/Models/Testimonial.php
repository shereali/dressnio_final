<?php

namespace App\Models;

use App\Traits\UserAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use HasFactory, UserAction, SoftDeletes;
    protected $guarded = [];


    public function media()
    {
        return $this->belongsTo(Media::class);
    }
}
