<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'country',
        'street_address',
        'extra_address',
        'city',
        'state',
        'zip_code',
        'latitude',
        'longitude',
        'addressable_id',
        'addressable_type',
        'place_id',
    ];

    protected $casts = [
        'city'      => 'integer',
        'latitude'  => 'double',
        'longitude' => 'double',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }

    public function cityDetails()
    {
        return $this->hasOne(City::class, 'id', 'city');
    }
}
