<?php

namespace App\Http\Notifications;

use App\Models\User;
use App\Models\UserVerification;
use App\Services\FirebaseService;

class UserVerificationNotification
{
    public static function send(UserVerification $userVerification)
    {
        // admin
        $admin_ids = User::where('role', 'admin')->pluck('id')->toArray();
        FirebaseService::sendToTopicAndStorage(
            'role-admin',
            $admin_ids,
            [
                'notifiable_id' => $userVerification->user_id,
                'notifiable_type' => 'user',
            ],
            'notifications.user.admin.verification.title',
            'notifications.user.admin.verification.body',
            [
                'user_id' => '#' . $userVerification->user_id,
                'full_name' => $userVerification->user->first_name . ' ' . $userVerification->user->last_name,
            ],
            [],
        );
    }
}
