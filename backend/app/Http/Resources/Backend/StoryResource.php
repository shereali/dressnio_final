<?php

namespace App\Http\Resources\Backend;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
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
            'id'   => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $this->media != null ? Helpers::publicPath($this->media->media_url ?? $this->media->name) : null,
            'products' => $this->products,
            'is_active' => $this->is_active ? true : false,

        ];
    }
}
