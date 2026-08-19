<?php

namespace App\Http\Resources\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
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
            'product_id'  => $this->product_id,
            'size_id'     => $this->size_id,
            'price_id'    => $this->price_id,
            'quantity'    => $this->quantity,
            'stock_in'    => $this->stock_in ? true : false,
            'stock_out'   => $this->stock_out ? true : false,
        ];
    }
}
