<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListingFeature extends Model
{
    use SoftDeletes;
    public $timestamps = false;

    protected $fillable = [
        'listing_id',
        'feature_id',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class)->withTrashed();
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class)->withTrashed();
    }
}
