<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                          => $this->id,
            'host_id'                    => $this->host_id,
            'title'                      => $this->title,
            'description'                => $this->description,
            'house_type_id'              => $this->house_type_id,
            'property_type'              => $this->property_type,
            'price'                      => $this->price,
            'currency'                   => $this->currency,
            'commission'                 => $this->commission,
            'service_fees'               => $this->service_fees,
            'status'                     => $this->status,
            'guests'                     => $this->guests,
            'bedrooms'                   => $this->bedrooms,
            'beds'                       => $this->beds,
            'bathrooms'                  => $this->bathrooms,
            'booking_capacity'           => $this->booking_capacity,
            'is_contains_cameras'        => $this->is_contains_cameras,
            'camera_locations'            => $this->camera_locations,
            'noise_monitoring_device'    => $this->noise_monitoring_device,
            'weapons_on_property'        => $this->weapons_on_property,
            'floor_number'               => $this->floor_number,
            'min_booking_days'           => $this->min_booking_days,
            'max_booking_days'           => $this->max_booking_days,
            'created_at'     => $this->created_at->format('Y-m-d H:i:s'),

            // علاقات عند التحميل
            'host'         => new UserResource($this->whenLoaded('host')),
            'house_type'   => new HouseTypeResource($this->whenLoaded('houseType')),
            'images'       => ImageResource::collection($this->whenLoaded('images')),
            'categories'   => CategoryResource::collection($this->whenLoaded('categories')),
            'features'     => FeatureResource::collection($this->whenLoaded('features')),
            'reviews'      => ListingReviewResource::collection($this->whenLoaded('reviews')),
            'available_dates' => ListingAvailableDateResource::collection($this->whenLoaded('availableDates')),
        ];
    }
}
