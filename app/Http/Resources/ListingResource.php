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
            'price_weekend'               => $this->price_weekend,
            'final_price'               => $this->final_price,
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
            'average_rating'              => $this->reviews->avg('rating') ?? null,
            'reviews_count'               => $this->reviews->count() ?? null,
            'min_booking_days'            => $this->min_booking_days,
            'max_booking_days'            => $this->max_booking_days,
            'is_published'                => $this->is_published,
            'vip'                         => $this->vip,
            'starts'                      => $this->starts,
            'created_at'                  => $this->created_at->format('Y-m-d H:i:s'),

            'is_favorite'                =>  $is_favorite,

            // علاقات عند التحميل
            'host'         => new UserResource($this->whenLoaded('host')),
            'house_type'   => new HouseTypeResource($this->whenLoaded('houseType')),
            'first_image'  => $this->firstImage() != null ? new ImageResource($this->firstImage()) : null,
            'images'       => ImageResource::collection($this->whenLoaded('images')),
            'categories'   => CategoryResource::collection($this->whenLoaded('categories')),
            'features'     => FeatureResource::collection($this->whenLoaded('features')),
            'reviews'      => ListingReviewResource::collection($this->whenLoaded('reviews')),
            'available_dates' => ListingAvailableDateResource::collection($this->whenLoaded('availableDates')),
            'similar_listings' => ListingResource::collection($this->whenLoaded('similarListings')),
            'orders' => $this->orders,
            'radius_distance' => 0.1,
            'address'      => $user == null || $user->isGuest() ? new AddressResource($this->addressWithRandomizedCoordinates()) : new AddressResource($this->whenLoaded('address')),
            // 'address'      => new AddressResource($this->whenLoaded('address')),
            'rule' => new ListingRuleResource($this->whenLoaded('rule')),
            // when load availableDates
            'available_dates_pro' =>  $this->whenLoaded('availableDates', function () {
                return $this->getAvailableDates();
            }),
            'not_available_dates' =>  $this->whenLoaded('availableDates', function () {
                return $this->notAvailableBetween();
            }),
        ];
    }
}
