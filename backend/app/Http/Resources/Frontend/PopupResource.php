<?php

namespace App\Http\Resources\Frontend;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PopupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'image'     => $this->media ? Helpers::publicPath($this->media->media_url ?? $this->media->name) : null,
            'url'       => $this->url,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
