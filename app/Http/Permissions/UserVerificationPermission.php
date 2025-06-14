<?php

use App\Models\User;
use App\Services\MessageService;

class UserVerificationPermission
{
    public static function index($query)
    {
        $user = User::auth();

        if (!$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }


    public static function show($userVerification)
    {
        $user = User::auth();

        if (!$user->isAdmin()) {
            if ($userVerification->user_id !== $user->id) {
                MessageService::abort(403, 'messages.permission.error');
            }
        }
    }

    public static function create($data)
    {
        $user = User::auth();

        if (!$user->isAdmin()) {
            $data['user_id'] = $user->id;
        }

        return $data;
    }

    public static function canUpdate($userVerification)
    {
        $user = User::auth();

        if (!$user->isAdmin()) {
            MessageService::abort(403, 'messages.permission.error');
        }
    }

    public static function canDelete($userVerification)
    {
        $user = User::auth();

        if (!$user->isAdmin()) {
            if ($userVerification->user_id !== $user->id) {
                MessageService::abort(403, 'messages.permission.error');
            }
        }

        if ($userVerification->status === 'approved') {
            MessageService::abort(403, 'messages.permission.error');
        }
    }
}
