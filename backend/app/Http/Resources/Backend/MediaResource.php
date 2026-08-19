<?php

namespace App\Http\Resources\Backend;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'category_id' => $this->category_id,
            'media_type'  => $this->media_type,
            'slug'        => $this->slug,
            'media_url'   => Helpers::publicPath($this->media_url ?? $this->name),
            'selected'    => false,
            'progress'    => 100,
            'uploaded'    => 100,
        ];
    }
}
