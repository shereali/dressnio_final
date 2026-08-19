<?php

namespace App\Http\Resources\Backend;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\Backend\ColorResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ColorSetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'colors'     => ColorResource::collection($this->colors),
            'slug'       => Str::slug($this->slug),
        ];
    }
}
