<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

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

    public static function auth()
    {
        if (Auth::check()) {
            return User::find(Auth::user()->id);
        }

        // MessageService::abort(503, 'messages.unauthorized');

        abort(
            401,
            response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ]),
        );
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }
}
