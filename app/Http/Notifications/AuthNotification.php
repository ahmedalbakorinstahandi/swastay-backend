<?php

namespace App\Http\Notifications;

use App\Models\User;
use App\Services\FirebaseService;

class AuthNotification
{
    public static function welcome($user)
    {
        //اشعارات الترحيب بعد التسجيل
        FirebaseService::sendToTopicAndStorage(
            'user-' . $user->id,
            [
                $user->id,
            ],
            [
                'notifiable_id' => $user->id,
                'notifiable_type' => 'general',
            ],
            'notifications.welcome.title',
            'notifications.welcome.body',
            [
                'first_name' => $user->first_name,
            ],
            [],
        );
    }

    public static function newUser($user)
    {
        $admin_ids = User::where('role', 'admin')->pluck('id')->toArray();
        FirebaseService::sendToTopicAndStorage(
            'role-admin',
            $admin_ids,
            [
                'notifiable_id' => $user->id,
                'notifiable_type' => 'user',
            ],
            'notifications.admin.user.new.title',
            'notifications.admin.user.new.body',
            [
                'user_id' => '#' . $user->id,
                'full_name' => $user->first_name . ' ' . $user->last_name,
            ],
            [],
        );
    }
}
