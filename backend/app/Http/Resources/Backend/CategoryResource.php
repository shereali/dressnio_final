<?php

namespace App\Http\Resources\Backend;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'children' => CategoryResource::collection($this->children),
            'image' => $this->media != null ? Helpers::publicPath($this->media->media_url ?? $this->media->name) : null,
            'is_active' => $this->is_active ? true : false,
            'selected' => false,
        ];
    }
}
