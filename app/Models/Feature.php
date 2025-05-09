<?php

namespace App\Models;

use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\LaravelPackageTools\Concerns\Package\HasTranslations;

class Feature extends Model
{
    use SoftDeletes, LanguageTrait, HasTranslations;

    protected $fillable = [
        'name',
        'icon',
    ];

    protected $casts = [
        'icon' => 'string',
    ];

    protected $translatable = [
        'name',
    ];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) =>  $this->getAllTranslations('name'),
        );
    }

    // علاقات
    public function listings()
    {
        return $this->belongsToMany(Listing::class, 'ListingFeatures');
    }
}
