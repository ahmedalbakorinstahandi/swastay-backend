<?php

namespace App\Http\Services;

use App\Http\Permissions\ListingPermission;
use App\Models\Address;
use App\Models\Image;
use App\Models\Listing;
use App\Models\Setting;
use App\Services\FilterService;
use App\Services\ImageService;
use App\Services\LanguageService;
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

        $list->load(['host', 'address', 'images', 'categories', 'features', 'reviews', 'availableDates', 'rule']);

        return $list;
    }

    // create
    public function create($data)
    {
        $data = LanguageService::prepareTranslatableData($data, new Listing);


        $data['currency'] = 'USD';
        $data['commission'] = Setting::where('key', 'commission')->first()->value;
        $data['status'] = 'draft';

        $listing = Listing::create($data);


        // images
        $images = $data['images'];
        foreach ($images as $image) {
            Image::create([
                'path' => $image,
                'type' => 'image',
                'imageable_id' => $listing->id,
                'imageable_type' => Listing::class,
            ]);
        }

        // address
        $location = $data['location'];

        $address = LocationService::getLocationData($location['latitude'], $location['longitude']);

        Address::create([
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

        $listing->load(['host', 'address', 'images', 'categories', 'features', 'reviews', 'availableDates', 'rule']);

        return $listing;
    }

    public function update($listing, $data)
    {

        $data = LanguageService::prepareTranslatableData($data, $listing);


        $images_count = count($listing->images);

        if (isset($data['images'])) {
            $images_count += count($data['images']);
        }

        if (isset($data['delete_images'])) {
            $images_count -= count($data['delete_images']);
        }

        if ($images_count < 5) {
            MessageService::abort(422, 'messages.listing.min_images_limit', ['limit' => 5]);
        }


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
                'street_address' => $address['address'] ?? '',
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
            // اريد حذف الغير موجودين في المصفوفة واضافة الجدد
            $listing->listingCategories()->whereNotIn('category_id', $categories)->delete();
            // معرفة المعرفات الجديدة
            $newCategories = array_diff($categories, $listing->categories()->pluck('category_id')->toArray());
            // اضافة الجدد
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
            // اريد حذف الغير موجودين في المصفوفة واضافة الجدد
            $listing->listingFeatures()->whereNotIn('feature_id', $features)->delete();
            // معرفة المعرفات الجديدة
            $newFeatures = array_diff($features, $listing->features()->pluck('feature_id')->toArray());
            // اضافة الجدد
            foreach ($newFeatures as $feature) {
                $listing->listingFeatures()->create([
                    'feature_id' => $feature,
                    'created_at' => now(),
                ]);
            }
        }


        $listing->load(['host', 'address', 'images', 'categories', 'features', 'reviews', 'availableDates', 'rule']);

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
}
