<?php

namespace App\Http\Services;

use App\Models\Listing;
use App\Models\Setting;
use App\Services\FilterService;
use App\Services\LocationService;
use App\Services\MessageService;

class ListingService
{
    public function index($data)
    {
        $query = Listing::with(['host', 'address', 'images']);

        $query = ListingPermission::filterIndex($query);


        $query = FilterService::applyFilters(
            $query,
            $data,
            ['title', 'description', 'houseType.name', 'houseType.description'],
            ['price', 'guests_count', 'bedrooms_count', 'beds_count', 'bathrooms_count', 'booking_capacity', 'floor_number', 'min_booking_days', 'max_booking_days'],
            [],
            ['host_id', 'house_type_id', 'status', 'is_contains_cameras', 'noise_monitoring_device'],
            ['host_id', 'status'],
        );


        return $query;
    }

    public function show($id)
    {
        $list = Listing::find($id);

        if (!$list) {
            MessageService::abort(404, 'messages.listing.not_found');
        }

        $list->load(['host', 'address', 'images', 'categories', 'features', 'reviews', 'availableDates', 'rules']);

        return $list;
    }

    // create
    public function create($data)
    {

        $data['currency'] = 'USD';
        $data['commission'] = Setting::where('key', 'commission')->first()->value;
        $data['status'] = 'draft';

        $listing = Listing::create($data);


        // images
        $images = $data['images'];
        foreach ($images as $image) {
            $listing->images()->create([
                'path' => $image,
                'type' => 'image',
                // 'imageable_id' => $listing->id,
                // 'imageable_type' => Listing::class,
            ]);
        }

        // address
        $location = $data['location'];

        $address = LocationService::getLocationData($location['latitude'], $location['longitude']);

        $listing->address()->create([
            'street_address' => $address['address'] ?? '',
            'city' => $address['city'] ?? '',
            'country' => $address['country'] ?? '',
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
            'extra_address' => $location['extra_address'],
            'state' => $address['state'] ?? '',
            'zip_code' => $address['postal_code'] ?? '',
            'addressable_id' => $listing->id,
            'addressable_type' => Listing::class,
        ]);

        // categories
        $categories = $data['categories'];

        foreach ($categories as $category) {
            $listing->categories()->attach($category);
        }

        // features
        $features = $data['features'] ?? [];

        foreach ($features as $feature) {
            $listing->features()->attach($feature);
        }

        $listing->load(['host', 'address', 'images', 'categories', 'features', 'reviews', 'availableDates', 'rules']);

        return $listing;
    }
}
