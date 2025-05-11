<?php

namespace App\Models;

use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ListingRule extends Model
{
    use HasTranslations, SoftDeletes, LanguageTrait;

    protected $fillable = [
        'listing_id',
        'allows_pets',
        'allows_smoking',
        'allows_parties',
        'allows_children',
        'remove_shoes',
        'no_extra_guests',
        'quiet_hours',
        'restricted_rooms_note',
        'check_in_time',
        'check_out_time',
        'garbage_disposal_note',
        'pool_usage_note',
        'forbidden_activities_note',
    ];

    protected $casts = [
        'allows_pets' => 'boolean',
        'allows_smoking' => 'boolean',
        'allows_parties' => 'boolean',
        'allows_children' => 'boolean',
        'remove_shoes' => 'boolean',
        'no_extra_guests' => 'boolean',
        // 'quiet_hours' => 'array',
        // 'restricted_rooms_note' => 'array',
        // 'garbage_disposal_note' => 'array',
        // 'pool_usage_note' => 'array',
        // 'forbidden_activities_note' => 'array',
    ];

    public $translatable = [
        'quiet_hours',
        'restricted_rooms_note',
        'garbage_disposal_note',
        'pool_usage_note',
        'forbidden_activities_note',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    protected function quietHours(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getAllTranslations('quiet_hours'),
        );
    }

    protected function restrictedRoomsNote(): Attribute
    {
        return Attribute::make(
            get: fn() =>   $this->getAllTranslations('restricted_rooms_note'),
        );
    }

    protected function garbageDisposalNote(): Attribute
    {
        return Attribute::make(
            get: fn() =>   $this->getAllTranslations('garbage_disposal_note'),
        );
    }

    protected function poolUsageNote(): Attribute
    {
        return Attribute::make(
            get: fn() =>   $this->getAllTranslations('pool_usage_note'),
        );
    }

    protected function forbiddenActivitiesNote(): Attribute
    {
        return Attribute::make(
            get: fn() =>   $this->getAllTranslations('forbidden_activities_note'),
        );
    }
}
