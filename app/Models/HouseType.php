<?php

namespace App\Models;

use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class HouseType extends Model
{
    use SoftDeletes, LanguageTrait, HasTranslations;

    protected $fillable = [
        'name',
        'icon',
        'description',
        'is_visible',
    ];


    protected $casts = [
        'icon' => 'string',
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

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}
