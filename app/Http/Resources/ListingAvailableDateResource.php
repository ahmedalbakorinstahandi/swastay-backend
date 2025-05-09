<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingAvailableDateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'listing_id'     => $this->listing_id,
            'available_date' => $this->available_date,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),

            'listing' => new ListingResource($this->whenLoaded('listing')),
        ];
    }
}
