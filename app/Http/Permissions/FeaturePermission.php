<?php

namespace App\Http\Permissions;

use App\Models\User;
use App\Services\MessageService;

class FeaturePermission
{

    public static function filterIndex($query)
    {
        $user = User::auth();
        if (!$user || !$user->isAdmin()) {
            $query->where('is_visible', true);
        }

        return $query;
    }

    public static function canShow($feature)
    {
        if (!$feature->is_visible) {
            $user = User::auth();
            if (!$user || !$user->isAdmin()) {
                MessageService::abort(403, 'messages.permission.error');
            }
        }
    }

    public static function canUpdate($feature)
    {
        $user = User::auth();
        if (!$user || !$user->isAdmin()) {
            MessageService::abort(403, 'messages.permission.error');
        }
    }

    public static function canDelete($feature)
    {
        $user = User::auth();
        if (!$user || !$user->isAdmin()) {
            MessageService::abort(403, 'messages.permission.error');
        }
    }
}
