<?php

namespace App\Http\Permissions;

use App\Models\User;
use App\Services\MessageService;

class UserPermission
{
    public static function canShow(User $user)
    {
        //
    }

    public static function canUpdate(User $user)
    {
        $auth = User::auth();
        if (!$auth || !$auth->isAdmin()) {
            MessageService::abort(403, 'messages.permission.error');
        }
    }

    public static function canDelete(User $user)
    {
        $auth = User::auth();
        if (!$auth || !$auth->isAdmin()) {
            MessageService::abort(403, 'messages.permission.error');
        }
    }
}
