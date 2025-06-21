<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingPrice extends Model
{
    use SoftDeletes;

    protected $fillable = ['booking_id', 'price', 'type', 'date'];

    protected $casts = [
        'price' => 'float',
        'date' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
