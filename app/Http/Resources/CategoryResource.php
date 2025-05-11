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
            'icon'  => $this->icon,
            'icon_url' => $this->icon ? asset('storage/' . $this->icon) : null,
            'key'   => $this->key,
            'is_visible' => $this->is_visible,

            'listings' => ListingResource::collection($this->whenLoaded('listings')),
        ];
    }
}
