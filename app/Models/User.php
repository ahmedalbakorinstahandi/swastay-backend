<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'wallet_balance',
        'avatar',
        'email',
        'email_verified',
        'country_code',
        'phone_number',
        'phone_verified',
        'password',
        'role',
        'status',
        'otp',
        'otp_expire_at',
        'is_verified',
    ];

    protected $casts = [
        'wallet_balance'     => 'float',
        'email_verified'     => 'boolean',
        'phone_verified'     => 'boolean',
        'is_verified'        => 'boolean',
        'otp_expire_at'      => 'datetime',
    ];

    // العلاقات
    public function listings()
    {
        return $this->hasMany(Listing::class, 'host_id');
    }

    public function bookingsAsGuest()
    {
        return $this->hasMany(Booking::class, 'guest_id');
    }

    public function bookingsAsHost()
    {
        return $this->hasMany(Booking::class, 'host_id');
    }
}
