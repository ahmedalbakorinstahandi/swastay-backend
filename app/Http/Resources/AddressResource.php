<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'country'        => $this->country,
            'street_address' => $this->street_address,
            'extra_address'  => $this->extra_address,
            'city'           => $this->city,
            'state'          => $this->state,
            'zip_code'       => $this->zip_code,
            'latitude'       => $this->latitude,
            'longitude'      => $this->longitude,
            'place_id'       => $this->place_id,
            'addressable_id'   => $this->addressable_id,
            'addressable_type' => $this->addressable_type,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
