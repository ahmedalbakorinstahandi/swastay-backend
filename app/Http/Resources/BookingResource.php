<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'listing_id'      => $this->listing_id,
            'host_id'         => $this->host_id,
            'guest_id'        => $this->guest_id,
            'start_date'      => $this->start_date,
            'end_date'        => $this->end_date,
            'check_in'        => $this->check_in,
            'check_out'       => $this->check_out,
            'status'          => $this->status,
            'currency'        => $this->currency,
            'price'           => $this->price,
            'commission'      => $this->commission,
            'service_fees'    => $this->service_fees,
            'message'         => $this->message,
            'adults_count'    => $this->adults_count,
            'children_count'  => $this->children_count,
            'infants_count'   => $this->infants_count,
            'pets_count'      => $this->pets_count,
            'host_notes'      => $this->host_notes,
            'admin_notes'     => $this->admin_notes,
            'created_at'      => $this->created_at->format('Y-m-d H:i:s'),

            // علاقات
            'listing' => new ListingResource($this->whenLoaded('listing')),
            'host'    => new UserResource($this->whenLoaded('host')),
            'guest'   => new UserResource($this->whenLoaded('guest')),
        ];
    }
}
