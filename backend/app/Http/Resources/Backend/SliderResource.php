<?php

namespace App\Http\Resources\Backend;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'media_id'    => $this->media_id,
            'image'       => $this->media != null ? Helpers::publicPath($this->media->media_url ?? $this->media->name) : null,
            'is_active'   => $this->is_active,
        ];
    }
}
