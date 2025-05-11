<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'description' => $this->description,
            'is_visible' => $this->is_visible,
            'icon'  => $this->icon,
            'key'   => $this->key,

            'listings' => ListingResource::collection($this->whenLoaded('listings')),
        ];
    }
}
