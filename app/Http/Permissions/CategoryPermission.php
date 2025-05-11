<?php

namespace App\Http\Permissions;

use App\Models\User;
use App\Services\MessageService;

class CategoryPermission
{
    public static function canShow($category)
    {
        if (!$category->is_visible) {
            $user = User::auth();

            if (!$user || !$user->isAdmin()) {
                MessageService::abort(403, 'messages.permission.error');
            }
        }
    }

    public static function canUpdate($category)
    {
        $user = User::auth();
        if (!$user || !$user->isAdmin()) {
            MessageService::abort(403, 'messages.permission.error');
        }
    }

    public static function canDelete($category)
    {
        $user = User::auth();
        if (!$user || !$user->isAdmin()) {
            MessageService::abort(403, 'messages.permission.error');
        }
    }
}
