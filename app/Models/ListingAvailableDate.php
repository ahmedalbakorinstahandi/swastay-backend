<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListingAvailableDate extends Model
{
    use SoftDeletes;
    
    public $timestamps = false;

    protected $fillable = [
        'listing_id',
        'available_date',
    ];

    protected $casts = [
        'available_date' => 'date',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class)->withTrashed();
    }
}
