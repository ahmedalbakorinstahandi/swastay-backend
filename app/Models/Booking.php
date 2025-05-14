<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $table = 'bookings';

    protected $fillable = [
        'listing_id',
        'host_id',
        'guest_id',
        'start_date',
        'end_date',
        'check_in',
        'check_out',
        'status',
        'currency',
        'price',
        'commission',
        'service_fees',
        'message',
        'adults_count',
        'children_count',
        'infants_count',
        'pets_count',
        'host_notes',
        'admin_notes',
    ];

    protected $casts = [
        'start_date'     => 'datetime',
        'end_date'       => 'datetime',
        'check_in'       => 'string',
        'check_out'      => 'string',
        'price'          => 'float',
        'commission'     => 'float',
        'service_fees'   => 'float',
        'adults_count'   => 'integer',
        'children_count' => 'integer',
        'infants_count'  => 'integer',
        'pets_count'     => 'integer',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class)->withTrashed();
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id')->withTrashed();
    }

    public function guest()
    {
        return $this->belongsTo(User::class, 'guest_id')->withTrashed();
    }

    public function review()
    {
        return $this->hasOne(ListingReview::class, 'booking_id', 'id');
    }
}
