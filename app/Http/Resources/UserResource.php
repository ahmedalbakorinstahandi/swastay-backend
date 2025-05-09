<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'              => $this->id,
            'first_name'      => $this->first_name,
            'last_name'       => $this->last_name,
            'wallet_balance'  => $this->wallet_balance,
            'avatar'          => $this->avatar,
            'email'           => $this->email,
            'email_verified'  => $this->email_verified,
            'country_code'    => $this->country_code,
            'phone_number'    => $this->phone_number,
            'phone_verified'  => $this->phone_verified,
            'role'            => $this->role,
            'status'          => $this->status,
            'otp'             => $this->otp,
            'otp_expire_at'   => $this->otp_expire_at,
            'is_verified'     => $this->is_verified,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),

            // علاقات
            'listings'           => ListingResource::collection($this->whenLoaded('listings')),
            'bookings_as_guest'  => BookingResource::collection($this->whenLoaded('bookingsAsGuest')),
            'bookings_as_host'   => BookingResource::collection($this->whenLoaded('bookingsAsHost')),
        ];
    }
}
