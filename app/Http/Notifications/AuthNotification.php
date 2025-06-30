<?php

namespace App\Http\Notifications;

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
}
