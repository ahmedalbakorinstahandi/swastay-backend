<?php

namespace App\Models;

use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
    use SoftDeletes, HasTranslations, LanguageTrait;

    protected $table = 'cities';

    protected $fillable = [
        'name',
        'place_id',
        'orders',
        'availability',
    ];

    public $translatable = [
        'name',
    ];

    protected $casts = [
        'availability' => 'boolean',
    ];



    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    // name
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) =>  $this->getAllTranslations('name'),
        );
    }
}
