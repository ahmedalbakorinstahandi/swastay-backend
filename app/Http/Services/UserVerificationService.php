<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\UserVerification;
use App\Services\MessageService;
use App\Services\FilterService;
use App\Http\Permissions\UserVerificationPermission;

class UserVerificationService
{
    public function index($filters = [])
    {
        $query = UserVerification::query();

        $query = UserVerificationPermission::index($query);

        return FilterService::applyFilters(
            $query,
            $filters,
            ['first_name', 'last_name', 'email', 'phone_number'],
            [],
            [],
            ['id_verified', 'is_verified'],
            ['id_verified', 'is_verified']
        );
    }

    public function show($id)
    {
        $user = UserVerification::where('id', $id)->first();

        if (!$user) {
            MessageService::abort(404, 'messages.user.not_found');
        }

        return $user;
    }

    public function create($data)
    {
        $user = User::auth();


        $data['status'] = 'in_review';

        $userVerification = UserVerification::create($data);


        if (!$user->isAdmin()) {
            if ($user->id_verified != 'none') {
                $user->id_verified = 'in_review';
                $user->save();

                // TODO: send notification to admin
            }
        }

        return $userVerification;
    }

    public function update(UserVerification $userVerification, array $data)
    {
        $userVerification->update($data);


        $user = User::auth();

        if ($user->isAdmin()) {
            $userVerification->reviewed_by = $user->id;
            $userVerification->reviewed_at = now();
        }

        return $userVerification;
    }

    public function destroy(UserVerification $userVerification)
    {
        $userVerification->delete();

        return $userVerification;
    }
}
