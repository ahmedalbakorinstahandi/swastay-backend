<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserListingFavoriteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'listing_id' => $this->listing_id,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),

            'user'    => new UserResource($this->whenLoaded('user')),
            'listing' => new ListingResource($this->whenLoaded('listing')),
        ];
    }
}
