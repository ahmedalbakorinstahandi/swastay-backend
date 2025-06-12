<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ListingResource extends JsonResource
{
    public function toArray($request)
    {

        $is_favorite = false;
        $user = User::Auth();
        if ($user) {

            $is_favorite = $user->favorites()->where('listing_id', $this->id)->exists();
        }

        return [
            'id'                          => $this->id,
            'host_id'                     => $this->host_id,
            'title'                       => $this->title,
            'description'                 => $this->description,
            'house_type_id'               => $this->house_type_id,
            'property_type'               => $this->property_type,
            'price'                       => $this->price,
            'currency'                    => $this->currency,
            'commission'                  => $this->commission,
            'status'                      => $this->status,
            'guests_count'                => $this->guests_count,
            'bedrooms_count'              => $this->bedrooms_count,
            'beds_count'                  => $this->beds_count,
            'bathrooms_count'             => $this->bathrooms_count,
            'booking_capacity'            => $this->booking_capacity,
            'is_contains_cameras'         => $this->is_contains_cameras,
            'camera_locations'            => $this->camera_locations,
            // 'noise_monitoring_device'     => $this->noise_monitoring_device,
            // 'weapons_on_property'         => $this->weapons_on_property,
            'floor_number'                => $this->floor_number,
            'min_booking_days'            => $this->min_booking_days,
            'max_booking_days'            => $this->max_booking_days,
            'is_published'                => $this->is_published,
            'created_at'                  => $this->created_at->format('Y-m-d H:i:s'),

            'is_favorite'                =>  $is_favorite,

            // علاقات عند التحميل
            'host'         => new UserResource($this->whenLoaded('host')),
            'house_type'   => new HouseTypeResource($this->whenLoaded('houseType')),
            'images'       => ImageResource::collection($this->whenLoaded('images')),
            'categories'   => CategoryResource::collection($this->whenLoaded('categories')),
            'features'     => FeatureResource::collection($this->whenLoaded('features')),
            'reviews'      => ListingReviewResource::collection($this->whenLoaded('reviews')),
            'available_dates' => ListingAvailableDateResource::collection($this->whenLoaded('availableDates')),
            'similar_listings' => ListingResource::collection($this->whenLoaded('similarListings')),
            'address'      => $user == null || $user->isGuest() ? new AddressResource($this->addressWithRandomizedCoordinates()) : new AddressResource($this->whenLoaded('address')),
            // 'address'      => new AddressResource($this->whenLoaded('address')),
            'rule' => new ListingRuleResource($this->whenLoaded('rule')),
            'available_dates_pro' => $this->getAvailableDates(),
        ];

        
    }


   
}



  