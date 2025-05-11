<?php

namespace App\Models;

use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{

    use SoftDeletes, LanguageTrait, HasTranslations;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'key',
        'is_visible',
    ];

    protected $casts = [
        'icon' => 'string',
        'key' => 'string',
        'is_visible' => 'boolean',
    ];

    protected $translatable = [
        'name',
        'description',
    ];


    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) =>  $this->getAllTranslations('name'),
        );
    }

    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) =>  $this->getAllTranslations('description'),
        );
    }

    // Relations
    public function listings()
    {
        return $this->belongsToMany(Listing::class, 'ListingCategories');
    }
}
