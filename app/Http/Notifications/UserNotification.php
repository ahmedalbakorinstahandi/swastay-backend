<?php

namespace App\Http\Notifications;

use App\Services\FirebaseService;
use App\Services\WhatsappMessageService;

class UserNotification
{
    public static function approvedVerification($user)
    {
        FirebaseService::sendToTopicAndStorage(
            'user-' . $user->id,
            [
                $user->id,
            ],
            [
                'notifiable_id' => $user->id,
                'notifiable_type' => 'user_verification',
            ],
            'notifications.user.verification.approved.title',
            'notifications.user.verification.approved.body',
            [
                'user_first_name' => $user->first_name,
            ],
            [],
        );

        // whatsapp message
        $message = __('notifications.user.verification.approved.message', [
            'user_first_name' => $user->first_name,
        ], $user->language);
        $phone = $user->country_code . $user->phone_number;

        WhatsappMessageService::send($phone, $message);
    }

    public static function rejectedVerification($user)
    {
        FirebaseService::sendToTopicAndStorage(
            'user-' . $user->id,
            [
                $user->id,
            ],
            [
                'notifiable_id' => $user->id,
                'notifiable_type' => 'user_verification',
            ],
            'notifications.user.verification.rejected.title',
            'notifications.user.verification.rejected.body',
            [
                'user_first_name' => $user->first_name,
            ],
        );

        // whatsapp message
        $message = __('notifications.user.verification.rejected.message', [
            'user_first_name' => $user->first_name,
        ], $user->language);
        $phone = $user->country_code . $user->phone_number;

        WhatsappMessageService::send($phone, $message);
    }

    public static function stoppedVerification($user)
    {
        FirebaseService::sendToTopicAndStorage(
            'user-' . $user->id,
            [
                $user->id,
            ],
            [
                'notifiable_id' => $user->id,
                'notifiable_type' => 'user_verification',
            ],
            'notifications.user.verification.stopped.title',
            'notifications.user.verification.stopped.body',
            [
                'user_first_name' => $user->first_name,
            ],
        );

        // whatsapp message
        $message = __('notifications.user.verification.stopped.message', [
            'user_first_name' => $user->first_name,
        ], $user->language);
        $phone = $user->country_code . $user->phone_number;
        WhatsappMessageService::send($phone, $message);
    }
}
