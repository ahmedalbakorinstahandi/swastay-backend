<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HouseTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'icon'  => $this->icon,
            'description' => $this->description,
            'is_visible' => $this->is_visible,

            'listings' => ListingResource::collection($this->whenLoaded('listings')),
        ];
    }
}
