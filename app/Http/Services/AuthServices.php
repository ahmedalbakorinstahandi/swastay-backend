<?php


namespace App\Http\Services;

use App\Models\User;
use App\Services\MessageService;
use App\Services\PhoneService;
use Illuminate\Support\Facades\Hash;

class AuthServices
{
    // login function with phone number
    public function login($data)
    {
        $inputPhone = str_replace(' ', '', $data['phone']);

        $user = User::whereRaw("REPLACE(CONCAT(country_code, phone_number), ' ', '') = ?", [$inputPhone])
            ->where('role', $data['role'])
            // ->where('status', 'active')
            ->where('phone_verified', true)
            // ->where('is_verified', true)
            ->first();

        if (!$user) {
            MessageService::abort(
                401,
                'auth.login_invalid',
            );
        }

        if ($user->status === 'banneded') {
            MessageService::abort(
                401,
                'auth.account_banned',
            );
        }

        if ($user->is_verified === false) {
            MessageService::abort(
                401,
                'auth.account_not_verified',
            );
        }

        if (!Hash::check($data['password'], $user->password)) {
            MessageService::abort(
                401,
                'auth.login_invalid',
            );
        }

        $token = $user->createToken($user->first_name . 'auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }


    // register function with phone number
    public function register($data)
    {
        // $inputPhone = str_replace(' ', '', $data['country_code'] . $data['phone_number']);

        // $user = User::whereRaw("REPLACE(CONCAT(country_code, phone_number), ' ', '') = ?", [$inputPhone])
        //     ->where('role', $data['role'])
        //     ->first();


        $phoneParts = PhoneService::parsePhoneParts($data['phone']);

        $data['country_code'] = $phoneParts['country_code'];
        $data['phone_number'] = $phoneParts['national_number'];

        $user = User::where('country_code', $data['country_code'])
            ->where('phone_number', $data['phone_number'])
            ->where('role', $data['role'])
            ->first();

        if ($user) {
            MessageService::abort(
                401,
                'auth.phone_number_already_exists',
            );
        }

        $otp = random_int(100000, 999999);
        $minutes = 10;
        $otpExpireAt = now()->addMinutes($minutes);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'wallet_balance' => 0.0,
            'avatar' => $data['avatar'] ?? null,
            'email' => '',
            'email_verified' => false,
            'country_code' => $data['country_code'],
            'phone_number' => $data['phone_number'],
            'phone_verified' => false,
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'status' => 'active',
            'otp' => $otp,
            'otp_expire_at' => $otpExpireAt,
            'is_verified' => false,
        ]);

        // Send OTP to phone number

        return [
            'user' => $user,
            'minutes' => $minutes,
            'otp_expire_at' => $otpExpireAt,
        ];
    }

    // verify otp function
    public function verifyOtp($data)
    {
        $inputPhone = str_replace(' ', '', $data['country_code'] . $data['phone_number']);

        $user = User::whereRaw("REPLACE(CONCAT(country_code, phone_number), ' ', '') = ?", [$inputPhone])
            ->where('role', $data['role'])
            ->first();

        if (!$user) {
            MessageService::abort(
                401,
                'auth.phone_number_not_found',
            );
        }

        if ($user->otp !== $data['otp']) {
            MessageService::abort(
                401,
                'auth.otp_invalid',
            );
        }

        if ($user->otp_expire_at < now()) {
            MessageService::abort(
                401,
                'auth.otp_expired',
            );
        }

        $user->update([
            'phone_verified' => true,
            'is_verified' => true,
            'otp' => null,
            'otp_expire_at' => null,
        ]);

        return $user;
    }
}
