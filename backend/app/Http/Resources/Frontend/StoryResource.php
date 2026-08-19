<?php

namespace App\Http\Resources\Frontend;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use App\Http\Resources\Frontend\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'slug'  => $this->slug,
            'products'  => ProductResource::collection($this->products),
            'image' => $this->media != null ? Helpers::publicPath($this->media->media_url ?? $this->media->name) : null,
        ];
    }
}
