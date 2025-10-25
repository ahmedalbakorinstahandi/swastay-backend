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
        'id_verified',
        'status',
        'bank_details',
        'otp',
        'otp_expire_at',
        'is_verified',
        'language',
    ];

    protected $casts = [
        'wallet_balance'     => 'float',
        'email_verified'     => 'boolean',
        'phone_verified'     => 'boolean',
        'is_verified'        => 'boolean',
        'otp_expire_at'      => 'datetime',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
        'language'           => 'string',
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

    // public function favorites()
    // {
    //     return $this->hasManyThrough(Listing::class, UserListingFavorite::class, 'user_id', 'id', 'id', 'listing_id');
    // }

    public function favorites()
    {
        return $this->hasMany(UserListingFavorite::class, 'user_id');
    }

    public function verifications()
    {
        return $this->hasMany(UserVerification::class, 'user_id');
    }


    public function isHost()
    {
        // return in_array($this->id_verified, ['approved', 'stopped']);

        $isFromHostEndpoint = Str::contains(Request::path(), 'api/host');

        return $isFromHostEndpoint &&  $this->role === 'user';
    }

    public function isGuest(): bool
    {
        $isNotApproved = !in_array($this->id_verified, ['approved', 'stopped']);
        $isFromGuestEndpoint = Str::contains(Request::path(), 'api/guest');

        // return ($isNotApproved || $isFromGuestEndpoint) && $this->role === 'user';
        return $isFromGuestEndpoint && $this->role === 'user';
    }

    // public static function auth()
    // {
    //     // Check if user is authenticated
    //     if (!Auth::guard('sanctum')->check()) {
    //         return null;
    //     }

    //     $token = request()->bearerToken();
    //     if (!$token) {
    //         return null;
    //     }

    //     $cacheKey = 'request_user_' . $token;
        
    //     // Get user from cache (stored by SetLocaleMiddleware)
    //     return cache()->get($cacheKey);
    // }

    public static function auth()
    {
        if (Auth::check()) {
            return User::find(Auth::user()->id);
        }


        return null;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }


    public static function notificationsUnreadCount()
    {
        $user = User::auth();
        if ($user) {
            return  Notification::where('user_id', $user->id)->whereNull('read_at')->count();
        } else {
            return  Notification::whereNull('user_id')->count();
        }
    }
}
