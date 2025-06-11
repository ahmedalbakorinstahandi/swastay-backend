<?php

namespace App\Http\Services;

use App\Http\Permissions\ListingPermission;
use App\Models\Address;
use App\Models\Image;
use App\Models\Listing;
use App\Models\ListingRule;
use App\Models\Setting;
use App\Models\User;
use App\Services\FilterService;
use App\Services\ImageService;
use App\Services\LanguageService;
use App\Services\LocationService;
use App\Services\MessageService;

class ListingService
{
    public function index($data)
    {
        $query = Listing::with(['host', 'address.cityDetails', 'images']);

        $query = ListingPermission::filterIndex($query);


        if (isset($data['get_favorites']) && $data['get_favorites'] == 1) {
            $user = User::auth();
            if ($user) {
                $query->whereHas('favorites', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

        if (isset($data['city_id'])) {
            $query->whereHas('address', function ($q) use ($data) {
                $q->where('city', $data['city_id']);
            });
        }

        $query = FilterService::applyFilters(
            $query,
            $data,
            ['title', 'description', 'houseType.name', 'houseType.description'],
            [
                'price',
                'guests_count',
                'bedrooms_count',
                'beds_count',
                'bathrooms_count',
                'booking_capacity',
                'floor_number',
                'min_booking_days',
                'max_booking_days',
                'address.city'
            ],
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

        $list->load(['host', 'address.cityDetails', 'images', 'categories', 'features', 'reviews', 'availableDates', 'rule']);

        return $list;
    }

    // create
    public function create($data)
    {
        $data = LanguageService::prepareTranslatableData($data, new Listing);


        $data['currency'] = 'USD';
        $data['commission'] = Setting::where('key', 'commission')->first()->value;
        $data['status'] = 'draft';
        $data['is_published'] = 1;

        $listing = Listing::create($data);


        // images
        if (isset($data['images'])) {

            $images = $data['images'] ?? [];
            foreach ($images as $image) {
                Image::create([
                    'path' => $image,
                    'type' => 'image',
                    'imageable_id' => $listing->id,
                    'imageable_type' => Listing::class,
                ]);
            }
        }

        // address
        $location = $data['location'];

        $address = LocationService::getLocationData($location['latitude'], $location['longitude']);

        Address::create([
            'street_address' => $location['street_address'] ?? '',
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
            $listing->listingCategories()->create([
                'category_id' => $category,
                'created_at' => now(),
            ]);
        }

        // features
        $features = $data['features'] ?? [];

        foreach ($features as $feature) {
            $listing->listingFeatures()->create([
                'feature_id' => $feature,
                'created_at' => now(),
            ]);
        }

        // create rule
        $listing->rule()->create([
            'listing_id' => $listing->id,
            'check_in_time' => $data['check_in_time'] ?? null,
            'check_out_time' => $data['check_out_time'] ?? null,
        ]);

        $listing->load(['host', 'address.cityDetails', 'images', 'categories', 'features', 'reviews', 'availableDates', 'rule']);

        return $listing;
    }

    public function update($listing, $data)
    {

        $data = LanguageService::prepareTranslatableData($data, $listing);


        //  $images_count = count($listing->images);

        //   if (isset($data['images'])) {
        //       $images_count += count($data['images']);
        //   }

        //   if (isset($data['delete_images'])) {
        //       $images_count -= count($data['delete_images']);
        //   }

        //  if ($images_count < 5) {
        //      MessageService::abort(422, 'messages.listing.min_images_limit', ['limit' => 5]);
        // }


        $listing->update($data);

        // images
        if (isset($data['images'])) {
            $images = $data['images'];
            foreach ($images as $image) {
                Image::create([
                    'path' => $image,
                    'type' => 'image',
                    'imageable_id' => $listing->id,
                    'imageable_type' => Listing::class,
                ]);
            }
        }

        // delete images
        if (isset($data['delete_images'])) {
            $deleteImages = $data['delete_images'];

            foreach ($deleteImages as $imageId) {
                $image = $listing->images()->find($imageId);
                if ($image) {
                    ImageService::deleteImage($image->path);
                    $listing->images()->where('id', $imageId)->delete();
                }
            }
        }

        // address
        if (isset($data['location'])) {
            $location = $data['location'];

            $address = LocationService::getLocationData($location['latitude'], $location['longitude']);

            $listing->address()->update([
                'street_address' => $location['street_address'] ?? '',
                'city' => $address['city'] ?? '',
                'country' => $address['country'] ?? '',
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'extra_address' => $location['extra_address'],
                'state' => $address['state'] ?? '',
                'zip_code' => $address['postal_code'] ?? '',
            ]);
        }

        // categories
        if (isset($data['categories'])) {
            $categories = $data['categories'];
            // $listing->categories()->sync($categories);
            $listing->listingCategories()->whereNotIn('category_id', $categories)->delete();
            $newCategories = array_diff($categories, $listing->categories()->pluck('category_id')->toArray());
            foreach ($newCategories as $category) {
                $listing->listingCategories()->create([
                    'category_id' => $category,
                    'created_at' => now(),
                ]);
            }
        }

        // features
        if (isset($data['features'])) {
            $features = $data['features'];
            // $listing->features()->sync($features);
            $listing->listingFeatures()->whereNotIn('feature_id', $features)->delete();
            $newFeatures = array_diff($features, $listing->features()->pluck('feature_id')->toArray());
            foreach ($newFeatures as $feature) {
                $listing->listingFeatures()->create([
                    'feature_id' => $feature,
                    'created_at' => now(),
                ]);
            }
        }

        // rule
        if (isset($data['check_in_time']) || isset($data['check_out_time'])) {
            $listing->rule()->update([
                'check_in_time' => $data['check_in_time'] ?? null,
                'check_out_time' => $data['check_out_time'] ?? null,
            ]);
        }


        $listing->load(['host', 'address.cityDetails', 'images', 'categories', 'features', 'reviews', 'availableDates', 'rule']);

        return $listing;
    }

    public function destroy($listing)
    {

        $listing->images()->each(function ($image) {
            ImageService::deleteImage($image->path);
            $image->delete();
        });

        $listing->address()->delete();

        $listing->categories()->detach();

        $listing->features()->detach();

        $listing->availableDates()->delete();

        $listing->rule()->delete();



        $listing->delete();
    }

    public function updateAvailableDate($listing, $data)
    {

        $newAvailableDates = $data['available_dates'] ?? [];
        $removedAvailableDates = $data['removed_available_dates'] ?? [];

        // remove available dates
        $listing->availableDates()->whereIn('available_date', $removedAvailableDates)->delete();


        // add available dates
        foreach ($newAvailableDates as $date) {
            $listing->availableDates()->updateOrCreate(
                [
                    'listing_id' => $listing->id,
                    'available_date' => $date,
                ],
                [
                    'created_at' => now(),
                ]
            );
        }

        $listing->load(['host', 'address.cityDetails', 'images', 'categories', 'features', 'reviews', 'availableDates', 'rule']);
        return $listing;
    }

    public function updateRule($listing, $data)
    {

        $data = LanguageService::prepareTranslatableData($data, new ListingRule);


        $rule = $listing->rule;

        if ($rule) {
            $rule->update($data);
        } else {
            $rule = $listing->rule()->create($data);
        }

        $listing->load(['host', 'address.cityDetails', 'images', 'categories', 'features', 'reviews', 'availableDates', 'rule']);

        return $listing;
    }
}
