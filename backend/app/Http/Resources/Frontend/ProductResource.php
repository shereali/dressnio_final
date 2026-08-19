<?php

namespace App\Http\Resources\Frontend;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $firstPrice = $this->price;

        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'slug'                => $this->slug,
            'cloth_type'          => $this->cloth_type,
            'fabric_type'         => $this->fabric_type,
            'image'               => count($this->media) > 0 ? Helpers::publicPath($this->media->first()->name) : null,
            'gallery'             => GalleryResource::collection($this->media),
            'description'         => $this->description,
            'sizes'               => $this->is_size_visible ? SizeResource::collection($this->sizes->unique('standard')) : [],
            'colors'              => $this->is_color_visible ? ColorResource::collection($this->colors) : [],
            'price'               => $firstPrice ? new PriceResource($firstPrice) : null,
            'prices'              => PriceResource::collection($this->prices),
            'stock_available'     => $this->stock_available,
            'is_active'           => (bool) $this->is_active,
            'is_featured'         => (bool) $this->is_featured,
            'new_arrival'         => (bool) $this->new_arrival,
            'best_seller'         => (bool) $this->best_seller,
            'highlight'           => (bool) $this->highlight,
            'fashionist_choice'   => (bool) $this->fashionist_choice,
            'has_color_variation' => (bool) $this->has_color_variation,
            'is_size_visible'     => (bool) $this->is_size_visible,
            'is_color_visible'    => (bool) $this->is_color_visible,
            'stock_status'        => $this->stock_status,
            'color_set_id'        => $this->color_set_id,
        ];
    }
}
