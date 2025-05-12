<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class User extends Model
{
    use SoftDeletes, HasApiTokens;

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
        'host_verified',
        'status',
        'bank_details',
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
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
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

    public function favorites()
    {
        return $this->hasMany(UserListingFavorite::class, 'user_id');
    }

    public function isHost()
    {
        return in_array($this->host_verified, ['approved', 'stopped']);
    }

    public function isGuest(): bool
    {
        $isNotApproved = !in_array($this->host_verified, ['approved', 'stopped']);
        $isFromGuestEndpoint = Str::contains(Request::path(), 'api/guest');

        return ($isNotApproved || $isFromGuestEndpoint) && $this->role === 'user';
    }

    public static function auth()
    {
        if (Auth::guard('sanctum')->check()) {
            $user =  Auth::guard('sanctum')->user();
            return User::find($user->id);
        }

        return null;
    }

    // public static function auth()
    // {
    //     if (Auth::check()) {
    //         return User::find(Auth::user()->id);
    //     }


    //     return null;
    // }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }
}
