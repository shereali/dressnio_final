<?php

namespace App\Http\Resources\Frontend;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->product;

        return [
            'id'         => $this->id,
            'product_id' => $this->product_id,
            'size_id'    => $this->size_id,
            'color_id'   => $this->color_id,
            'quantity'   => $this->quantity,
            'size'       => $this->size?->standard,
            'color'      => $this->color?->color_name,
            'color_code' => $this->color?->color_code,
            'product'    => $product ? [
                'id'          => $product->id,
                'name'        => $product->name,
                'slug'        => $product->slug,
                'image'       => $product->media->first() ? Helpers::publicPath($product->media->first()->media_url ?? $product->media->first()->name) : null,
                'price'       => $product->price?->price,
                'discount'    => $product->price?->discount ?? 0,
            ] : null,
            'line_total' => ($product?->price?->price ?? 0) * $this->quantity,
        ];
    }
}
