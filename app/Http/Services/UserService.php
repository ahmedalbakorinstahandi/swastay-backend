<?php

namespace App\Http\Services;

use App\Models\User;
use App\Services\FilterService;
use App\Services\MessageService;
use App\Services\PhoneService;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function index($filters = [])
    {
        return FilterService::applyFilters(
            User::query(),
            $filters,
            [['first_name', 'last_name'], 'email', 'phone_number'],
            ['wallet_balance'],
            [],
            ['role', 'status', 'host_verified', 'is_verified'],
            ['role', 'status']
        );
    }

    public function show($id): User
    {
        $user = User::find($id)->first();

        if (!$user) {
            MessageService::abort(404, 'messages.user.not_found');
        }

        return $user;
    }

    public function create($data)
    {
        $data['password'] = Hash::make($data['password']);


        $phoneParts = PhoneService::parsePhoneParts($data['phone']);

        $data['country_code'] = $phoneParts['country_code'];
        $data['phone_number'] = $phoneParts['national_number'];
        $data['phone_verified'] = true;
        $data['is_verified'] = true;

        if (empty($data['email'])) {
            $data['email'] = '';
        } else {
            $data['email_verified'] = true;
        }

        $data['wallet_balance'] = 0;
        $data['role'] = 'user';
        $data['status'] = 'active';

        return User::create($data);
    }

    public function update(User $user, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if (isset($data['phone'])) {
            $phoneParts = PhoneService::parsePhoneParts($data['phone']);

            $data['country_code'] = $phoneParts['country_code'];
            $data['phone_number'] = $phoneParts['national_number'];
        }

        if (isset($data['email'])) {
            $email_exists = User::whereEmail($data['email'])
                ->where('id', '!=', $user->id)
                ->whereNull('deleted_at')
                ->first();

            if ($email_exists) {
                MessageService::abort(422, 'messages.user.email_exists');
            }
        }

        $user->update($data);

        return $user;
    }

    public function destroy(User $user)
    {
        $user->delete();
    }
}
