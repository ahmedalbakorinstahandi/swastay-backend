<?php

namespace App\Http\Services;

use App\Http\Permissions\ListingPermission;
use App\Http\Notifications\ListingNotification;
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
use App\Services\OrderHelper;

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

            if ($data['city_id'] == 2) {
                $query->whereHas('address', function ($q) use ($data) {
                    $q->whereIn('city', [2, 14]);
                });
            } else {
                $query->whereHas('address', function ($q) use ($data) {
                    $q->where('city', $data['city_id']);
                });
            }
        }

        $data['sort_field'] = $data['sort_field'] ?? 'orders';
        $data['sort_order'] = $data['sort_order'] ?? 'asc';

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
            ['host_id', 'house_type_id', 'status', 'is_contains_cameras', 'vip', 'starts', 'bedrooms_count'],
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

        $list->load(['host', 'address.cityDetails', 'images', 'categories', 'features', 'reviews.user', 'availableDates', 'rule']);

        $user = User::auth();
        if (!$user || $user->isGuest()) {
            $list->load(['similarListings.images', 'similarListings.address.cityDetails', 'similarListings.host']);
        }

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

        ListingNotification::listingCreated($listing);

        OrderHelper::assign($listing);


        // images
        if (isset($data['images'])) {

            $images = $data['images'] ?? [];
            foreach ($images as $image) {
                $image =  Image::create([
                    'path' => $image,
                    'type' => 'image',
                    'imageable_id' => $listing->id,
                    'imageable_type' => Listing::class,
                ]);
                OrderHelper::assign($image);
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
            'allows_families_only' => $data['rule']['allows_families_only'] ?? null,
        ]);
        $listing->load(['host', 'address.cityDetails', 'images', 'categories', 'features', 'reviews.user', 'availableDates', 'rule']);



        return $listing;
    }

    public function update($listing, $data)
    {

        $data = LanguageService::prepareTranslatableData($data, $listing);


        $last_status = $listing->status;

        $listing->update($data);


        //"draft", "in_review", "approved", "paused", "rejected"
        if ($last_status != $listing->status) {
            if ($listing->status == 'approved') {
                ListingNotification::listingApproved($listing);

                // ListingNotification::listingFirstCreated($listing);
            } else if ($listing->status == 'rejected') {
                ListingNotification::listingRejected($listing);
            } else if ($listing->status == 'paused') {
                ListingNotification::listingPaused($listing);
            }
        }

        // images
        if (isset($data['images'])) {
            $images = $data['images'];
            foreach ($images as $image) {
                // Check if image already exists for this listing
                $existingImage = $listing->images()->where('path', $image)->first();

                if (!$existingImage) {
                    $image = Image::create([
                        'path' => $image,
                        'type' => 'image',
                        'imageable_id' => $listing->id,
                        'imageable_type' => Listing::class,
                    ]);
                    OrderHelper::assign($image);
                }
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

        $listing->rule()->update([
            'check_in_time' => $data['check_in_time'] ?? null,
            'check_out_time' => $data['check_out_time'] ?? null,
            'allows_families_only' => $data['rule']['allows_families_only'] ?? null,
        ]);



        $listing->load(['host', 'address.cityDetails', 'images', 'categories', 'features', 'reviews.user', 'availableDates', 'rule']);

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

        $listing->load(['host', 'address.cityDetails', 'images', 'categories', 'features', 'reviews.user', 'availableDates', 'rule']);
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

        $listing->load(['host', 'address.cityDetails', 'images', 'categories', 'features', 'reviews.user', 'availableDates', 'rule']);

        return $listing;
    }

    public function reorderListing($listing, $data)
    {

        $listing_index = $data['listing_index'];

        $listing_ids = Listing::withTrashed()->orderBy('id', 'asc')->pluck('id')->toArray();

        // abort(
        //     response()->json([
        //         'message' => 'Listing index is out of range',
        //         'status' => 422,
        //         'data' => $listing_ids,
        //         'listing_index' => $listing_index,
        //         'listings_count' => count($listing_ids),
        //     ], 422)
        // );

        $listing_selected_id = $listing_ids[$listing_index - 1];

        $listing_selected = Listing::withTrashed()->find($listing_selected_id);


        OrderHelper::reorder($listing, $listing_selected->orders, 'orders');


        $listing->load(['host', 'address.cityDetails', 'images', 'categories', 'features', 'reviews.user', 'availableDates', 'rule']);

        return $listing;
    }
}
