<?php

namespace App\Http\Services;

use App\Models\User;
use App\Services\FilterService;
use App\Services\MessageService;
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

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            MessageService::abort(404, 'messages.user.not_found');
        }
        return $user;
    }

    public function create($data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function update(User $user, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return $user;
    }

    public function destroy(User $user)
    {
        $user->delete();
    }
}
