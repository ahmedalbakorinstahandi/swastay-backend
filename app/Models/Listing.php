<?php

namespace App\Models;

use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

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
        'is_published',
        'guests_count',
        'bedrooms_count',
        'beds_count',
        'bathrooms_count',
        'booking_capacity',
        'is_contains_cameras',
        'camera_locations',
        // 'noise_monitoring_device',
        // 'weapons_on_property',
        'floor_number',
        'min_booking_days',
        'max_booking_days',
        'addedd_by',
    ];

    protected $casts = [
        'price' => 'float',
        'commission' => 'float',
        'guests_count' => 'integer',
        'bedrooms_count' => 'integer',
        'beds_count' => 'integer',
        'bathrooms_count' => 'float',
        'booking_capacity' => 'integer',
        // 'noise_monitoring_device' => 'boolean',
        // 'weapons_on_property' => 'boolean',
        'floor_number' => 'integer',
        'min_booking_days' => 'integer',
        'max_booking_days' => 'integer',
        'is_contains_cameras' => 'boolean',
        'addedd_by' => 'integer',
    ];

    protected $translatable = [
        'title',
        'description',
        'camera_locations',
    ];


    public function similarListings()
    {
        // same city or same house type and status approved and is_published true
        return $this->hasMany(Listing::class, 'house_type_id', 'house_type_id')
            ->where('id', '!=', $this->id)
            ->whereHas('address', function ($query) {
                $query->when($this->address, function ($q) {
                    $q->where('city', $this->address->city);
                });
            })
            ->where('status', 'approved')
            ->where('is_published', true)
            ->limit(3);
    }

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

    public function images() // morphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    // listing categories
    public function listingCategories()
    {
        return $this->hasMany(ListingCategory::class);
    }
    public function listingFeatures()
    {
        return $this->hasMany(ListingFeature::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'listing_categories', 'listing_id', 'category_id');
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'listing_features', 'listing_id', 'feature_id');
    }

    public function reviews()
    {
        return $this->hasManyThrough(ListingReview::class, Booking::class, 'listing_id', 'booking_id', 'id', 'id');
    }

    public function availableDates()
    {
        return $this->hasMany(ListingAvailableDate::class);
    }

    public function favorites()
    {
        return $this->hasMany(UserListingFavorite::class);
    }

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }


    
     // distance attribute
     public function getDistanceAttribute()
     {
        return 0.1;
     }

    public function addressWithRandomizedCoordinates()
    {
        $address = $this->address()->first();

        if ($address && $address->latitude && $address->longitude) {
            $earthRadius = 6371;
            $maxDistance = $this->distance ?? 0.1; // km

            $distance = mt_rand(0, $maxDistance * 1000) / 1000;
            $angle = mt_rand(0, 360);
            $angleRad = deg2rad($angle);

            $newLatitude = $address->latitude + ($distance / $earthRadius) * (180 / pi()) * cos($angleRad);
            $newLongitude = $address->longitude + ($distance / $earthRadius) * (180 / pi()) * sin($angleRad) / cos(deg2rad($address->latitude));

            $address->latitude = $newLatitude;
            $address->longitude = $newLongitude;
        }

        return $address;
    }


    public function rule()
    {
        return $this->hasOne(ListingRule::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }



    public function isAvailableBetween($startDate, $endDate): bool
    {
        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end = \Carbon\Carbon::parse($endDate)->startOfDay();
        $days = $start->diffInDays($end);

        if ($days < $this->min_booking_days) {
            return false;
        }

        $dateRange = collect();
        for ($date = $start->copy(); $date < $end; $date->addDay()) {
            $dateRange->push($date->toDateString());
        }

        // الأيام المتوفرة بالقائمة
        $availableDays = $this->availableDates()
            ->whereBetween('available_date', [$start, $end])
            ->pluck('available_date')
            ->map(fn($d) => $d->toDateString());

        if ($availableDays->count() < $dateRange->count()) {
            return false;
        }

        // الحجوزات المؤكدة
        $confirmedDates = $this->bookings()
            ->whereIn('status', ['confirmed'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_date', '<', $start)->where('end_date', '>', $end);
                    });
            })
            ->get()
            ->flatMap(function ($booking) {
                return collect(\Carbon\CarbonPeriod::create($booking->start_date, $booking->end_date))
                    ->map(fn($date) => $date->toDateString());
            });

        if ($dateRange->intersect($confirmedDates)->isNotEmpty()) {
            return false;
        }

        // التحقق من accepted حسب الـ capacity
        $acceptedBookings = $this->bookings()
            ->where('status', 'accepted')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_date', '<', $start)->where('end_date', '>', $end);
                    });
            })
            ->get();

        $acceptedDates = [];

        foreach ($acceptedBookings as $booking) {
            $period = \Carbon\CarbonPeriod::create($booking->start_date, $booking->end_date);
            foreach ($period as $date) {
                $acceptedDates[$date->toDateString()] = ($acceptedDates[$date->toDateString()] ?? 0) + 1;
                if ($acceptedDates[$date->toDateString()] > $this->booking_capacity) {
                    return false;
                }
            }
        }

        return true;
    }


    public function getBlockedDates(): array
    {
        $blocked = [];

        // غير متوفرة: ليست موجودة ضمن availableDates
        $allDates = $this->availableDates()->pluck('available_date')->map(fn($d) => $d->toDateString())->toArray();

        // التواريخ المستقبلية (مثلاً سنة قادمة)
        $start = now();
        $end = now()->addYear();

        $expected = [];
        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $expected[] = $date->toDateString();
        }

        $notAvailable = array_diff($expected, $allDates);
        $blocked = array_merge($blocked, $notAvailable);

        // التواريخ المؤكدة
        $confirmed = $this->bookings()
            ->where('status', 'confirmed')
            ->get()
            ->flatMap(function ($booking) {
                return collect(\Carbon\CarbonPeriod::create($booking->start_date, $booking->end_date))
                    ->map(fn($d) => $d->toDateString());
            });

        $blocked = array_merge($blocked, $confirmed->toArray());

        // التواريخ التي تجاوزت الـ booking_capacity ضمن accepted
        $accepted = $this->bookings()
            ->where('status', 'accepted')
            ->get()
            ->flatMap(function ($booking) {
                return collect(\Carbon\CarbonPeriod::create($booking->start_date, $booking->end_date))
                    ->map(fn($d) => $d->toDateString());
            })
            ->groupBy(fn($date) => $date)
            ->filter(fn($dates) => count($dates) >= $this->booking_capacity)
            ->keys();

        $blocked = array_merge($blocked, $accepted->toArray());

        return array_unique($blocked);
    }

    public function getAvailableDates(): array
    {
        // 1. كل الأيام المتاحة في الجدول
        $available = $this->availableDates()
            ->pluck('available_date')
            ->map(fn($d) => $d->toDateString())
            ->toArray();

        // 2. أيام الحجز المؤكدة
        $confirmed = $this->bookings()
            ->where('status', 'confirmed')
            ->get()
            ->flatMap(function ($booking) {
                return collect(\Carbon\CarbonPeriod::create($booking->start_date, $booking->end_date))
                    ->map(fn($date) => $date->toDateString());
            })
            ->toArray();

        // 3. أيام الحجوزات accepted التي تجاوزت السعة
        $acceptedOverCapacity = $this->bookings()
            ->where('status', 'accepted')
            ->get()
            ->flatMap(function ($booking) {
                return collect(\Carbon\CarbonPeriod::create($booking->start_date, $booking->end_date))
                    ->map(fn($d) => $d->toDateString());
            })
            ->groupBy(fn($date) => $date)
            ->filter(fn($group) => count($group) >= $this->booking_capacity)
            ->keys()
            ->toArray();

        // 4. جمع كل المحجوز والممنوع
        $blocked = array_unique(array_merge($confirmed, $acceptedOverCapacity));

        // 5. المتاحة = available - blocked
        return array_values(array_diff($available, $blocked));
    }


    // public function canReview($userId): bool
    // {

    //     $user = User::auth();

    //     if (!$user || $user->isAdmin() || $user->isHost()) {
    //         return false;
    //     }

    //     return $this->reviews()
    //         ->where('user_id', $userId)
    //         ->whereHas('bookings', function ($q) {
    //             $q->where('status', 'confirmed');
    //         })
    //         ->doesntExist();
    // }
}
