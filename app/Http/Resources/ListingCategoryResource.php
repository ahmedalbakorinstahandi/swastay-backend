<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'listing_id'  => $this->listing_id,
            'category_id' => $this->category_id,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),

            'listing'  => new ListingResource($this->whenLoaded('listing')),
            'category' => new CategoryResource($this->whenLoaded('category')),
        ];
    }
}
