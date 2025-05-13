<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
    use SoftDeletes, HasTranslations;

    protected $table = 'cities';

    protected $fillable = [
        'name',
        'place_id',
    ];

    public $translatable = [
        'name',
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
