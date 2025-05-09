<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingFeatureResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'listing_id' => $this->listing_id,
            'feature_id' => $this->feature_id,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),

            'listing' => new ListingResource($this->whenLoaded('listing')),
            'feature' => new FeatureResource($this->whenLoaded('feature')),
        ];
    }
}
