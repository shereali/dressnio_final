<?php

namespace App\Http\Resources\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SizeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'size_type' => $this->size_type,
            'dressType' => $this->size_type == 1 ? 'Kameez' : 'Salwar',
            'standard'  => $this->standard,
            'bust'      => $this->bust,
            'lengths'   => $this->lengths,
            'waist'     => $this->waist,
            'hip'       => $this->hip,
            'thighs'    => $this->thighs,
        ];
    }
}
