<?php

namespace App\Http\Resources\Backend;

use Illuminate\Http\Request;
use App\Http\Resources\Backend\SizeResource;
use App\Http\Resources\Backend\MediaResource;
use App\Http\Resources\Backend\StockResource;
use App\Http\Resources\Backend\CategoryResource;
use App\Http\Resources\Backend\ColorSetResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'slug'                => $this->slug,
            'price'               => new PriceResource($this->price),
            'media'               => MediaResource::collection($this->media),
            'prices'              => PriceResource::collection($this->prices),
            'categories'          => CategoryResource::collection($this->categories),
            'sizes'               => SizeResource::collection($this->sizes),
            'colors'              => ColorResource::collection($this->colors),
            'description'         => $this->description,
            'is_active'           => $this->is_active,
            'fashionist_choice'   => $this->fashionist_choice ? true : false,
            'is_featured'         => $this->is_featured ? true      : false,
            'new_arrival'         => $this->new_arrival ? true      : false,
            'best_seller'         => $this->best_seller ? true      : false,
            'stock_status'        => $this->stock_status,
            'fabric_type'         => $this->fabric_type,
            'cloth_type'          => $this->cloth_type,
            'highlight'           => $this->highlight ? true        : false,
            'color_set_id'        => $this->color_set_id,
            'colorSet'            => new ColorSetResource($this->colorSet),
            'is_color_visible'    => $this->is_color_visible,
            'is_size_visible'     => $this->is_size_visible,
            'has_color_variation' => $this->has_color_variation,
            'stocks'              => StockResource::collection($this->stocks),
        ];
    }
}
