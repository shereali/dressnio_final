<?php

namespace App\Http\Resources\Frontend;

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
        $mediaUrl = $this->media ? ($this->media->media_url ?? $this->media->url ?? $this->media->file_name ?? $this->media->name) : ($this->image ?? $this->picture ?? null);

        return [
            'id'       => $this->id,
            'name'     => $this->name ?? $this->title,
            'slug'     => $this->slug,
            'image'    => $mediaUrl ? Helpers::publicPath($mediaUrl) : null,
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
