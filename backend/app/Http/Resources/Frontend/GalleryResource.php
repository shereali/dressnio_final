<?php

namespace App\Http\Resources\Frontend;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'url'  => Helpers::publicPath($this->media_url ?? $this->name),
        ];
    }
}
