<?php

namespace App\Http\Services;

use App\Services\FilterService;
use App\Services\MessageService;
use App\Http\Permissions\NotificationPermission;
use App\Models\Notification;
use App\Models\User;
use App\Services\FirebaseService;

class NotificationService
{
    public function index($data)
    {
        $query = Notification::query()->with(['user']);

        $query = NotificationPermission::filterIndex($query);

        return FilterService::applyFilters(
            $query,
            $data,
            ['title', 'message'],
            [],
            ['created_at'],
            ['user_id'],
            ['id']
        );
    }

    public function show($id)
    {
        $notification = Notification::with(['user'])->find($id);

        if (!$notification) {
            MessageService::abort(
                404,
                'messages.notification.item_not_found'
            );
        }

        return $notification;
    }

    public function create($validatedData)
    {
        return Notification::create($validatedData);
    }


    public function update($notification, $validatedData)
    {
        $notification->update($validatedData);


        return $notification;
    }

    public function destroy($notification)
    {

        return $notification->delete();
    }


    public static function storeNotification($users_ids, $notificationable, $title, $body, $replace, $data = [], $isCustom = false)
    {
        $notificationService = new NotificationService();
        $locales = config('translatable.locales');

        foreach ($users_ids as $user_id) {
            $notificationData = [
                'user_id' => $user_id,
                'title' => [],
                'message' => [],
                'notificationable_id' => $notificationable['id'] ?? null,
                'notificationable_type' => $notificationable['type'] ?? 'custom',
                'metadata' => [
                    'data' => $data,
                    'replace' => $replace,
                    'notificationable' => $notificationable,
                ],
            ];

            if ($isCustom) {
                $notificationData['title']['cu'] = $title;
                $notificationData['message']['cu'] = $body;
            } else {
                foreach ($locales as $locale) {
                    $notificationData['title'][$locale] = __($title, $replace, $locale);
                    $notificationData['message'][$locale] = __($body, $replace, $locale);
                }
            }

            $notificationService->create($notificationData);
        }
    }




    public function sendNotificationToAllUsers($data)
    {

        FirebaseService::sendToTopicAndStorage(
            'all-users',
            [null],
            [
                'id' => null,
                'type' => User::class,
            ],
            $data['title'],
            $data['message'],
            [],
            [],
            true,
        );


        $last_notification = Notification::whereNull('user_id')->latest()->first();

        return $last_notification;
    }


    public function readNotification($id)
    {

        $user = User::auth();
        $notifications = Notification::where('id', '<=', $id)->where('read_at', null)->where('user_id', $user->id)->get();

        foreach ($notifications as $notification) {
            $notification->update(['read_at' => now()]);
        }

        return $notifications;
    }
}
