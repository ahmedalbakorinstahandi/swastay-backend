<?php


namespace App\Http\Services;

use App\Models\User;
use App\Services\MessageService;
use App\Services\PhoneService;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthServices
{
    public function login($data)
    {

        $phoneParts = PhoneService::parsePhoneParts($data['phone']);

        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];

        $user = User::where('country_code', $countryCode)
            ->where('phone_number', $phoneNumber)
            ->where('role', $data['role'])
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


    public function register($data)
    {

        $phoneParts = PhoneService::parsePhoneParts($data['phone']);

        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];

        $user = User::where('country_code', $countryCode)
            ->where('phone_number', $phoneNumber)
            ->where('role', 'user')
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
            'country_code' => $countryCode,
            'phone_number' => $phoneNumber,
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

    public function verifyOtp($data)
    {

        $phoneParts = PhoneService::parsePhoneParts($data['phone']);

        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];

        $user = User::where('country_code', $countryCode)
            ->where('phone_number', $phoneNumber)
            ->where('role', 'user')
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

        return [
            'user' => $user,
            'token' => $user->createToken($user->first_name)->plainTextToken,
        ];
    }

    public function forgotPassword($data)
    {
        $phoneParts = PhoneService::parsePhoneParts($data['phone']);

        $countryCode = $phoneParts['country_code'];
        $phoneNumber = $phoneParts['national_number'];

        $user = User::where('country_code', $countryCode)
            ->where('phone_number', $phoneNumber)
            ->where('role', 'user')
            ->first();

        if (!$user) {
            MessageService::abort(
                401,
                'auth.phone_number_not_found',
            );
        }

        $code = random_int(100000, 999999);
        $minutes = 10;
        $otpExpireAt = now()->addMinutes($minutes);
        $user->update([
            'otp' => $code,
            'otp_expire_at' => $otpExpireAt,
        ]);

        // Send OTP to phone number

        return [
            'user' => $user,
            'minutes' => 10,
            'otp_expire_at' =>  $otpExpireAt,
        ];
    }

    public function resetPassword($data)
    {
        $user = User::auth();

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        $newToken = $user->createToken($user->first_name)->plainTextToken;

        return [
            'user' => $user,
            'token' => $newToken,
        ];
    }

    public function logout($token)
    {
        $personalAccessToken = PersonalAccessToken::findToken($token);

        // FirebaseService::unsubscribeFromAllTopic($personalAccessToken->tokenable);

        return $personalAccessToken->delete();
    }
}
