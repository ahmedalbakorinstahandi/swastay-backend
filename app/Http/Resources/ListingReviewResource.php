<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'booking_id' => $this->booking_id,
            'user_id'    => $this->user_id,
            'comment'    => $this->comment,
            'rating'     => $this->rating,
            'blocked_at' => $this->blocked_at,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),

            'booking' => new BookingResource($this->whenLoaded('booking')),
            'user'    => new UserResource($this->whenLoaded('user')),
        ];
    }
}
