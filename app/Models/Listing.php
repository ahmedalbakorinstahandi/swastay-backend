<?php

namespace App\Models;

use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\LaravelPackageTools\Concerns\Package\HasTranslations;

class Listing extends Model
{
    use SoftDeletes, LanguageTrait, HasTranslations;

    protected $fillable = [
        'host_id',
        'title',
        'description',
        'house_type_id',
        'property_type',
        'price',
        'currency',
        'commission',
        'status',
        'guests_count',
        'bedrooms_count',
        'beds_count',
        'bathrooms_count',
        'booking_capacity',
        'is_contains_cameras',
        'camera_locations',
        'noise_monitoring_device',
        'weapons_on_property',
        'floor_number',
        'min_booking_days',
        'max_booking_days',
    ];

    protected $casts = [
        'price' => 'float',
        'commission' => 'float',
        'guests_count' => 'integer',
        'bedrooms_count' => 'integer',
        'beds_count' => 'integer',
        'bathrooms_count' => 'float',
        'booking_capacity' => 'integer',
        'noise_monitoring_device' => 'boolean',
        'weapons_on_property' => 'boolean',
        'floor_number' => 'integer',
        'min_booking_days' => 'integer',
        'max_booking_days' => 'integer',
        'is_contains_cameras' => 'boolean',
    ];

    protected $translatable = [
        'title',
        'description',
        'camera_locations',
    ];

    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => $this->getAllTranslations('title'),
        );
    }

    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => $this->getAllTranslations('description'),
        );
    }

    protected function cameraLocations(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => $this->getAllTranslations('camera_locations'),
        );
    }

    // العلاقات
    public function host()
    {
        return $this->belongsTo(User::class, 'host_id')->withTrashed();
    }

    public function houseType()
    {
        return $this->belongsTo(HouseType::class)->withTrashed();
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'ListingCategories');
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'ListingFeatures');
    }

    public function reviews()
    {
        return $this->hasMany(ListingReview::class);
    }

    public function availableDates()
    {
        return $this->hasMany(ListingAvailableDate::class);
    }
}
