<?php


namespace App\Http\Permissions;

use App\Models\User;
use App\Services\MessageService;

class BookingPermission
{


    public static function filterIndex($query)
    {
        $user = User::auth();

        if ($user) {
            if ($user->isHost()) {
                $query->where('host_id', $user->id);
            }

            if ($user->isGuest()) {
                $query->where('guest_id', $user->id);
            }

            return $query;
        }

        return [];
    }


    public static function canShow($booking)
    {
        $user = User::auth();

        if (!$user) {
            MessageService::abort(403, 'messages.permission.error');
        }

        if ($user->isHost() && $booking->host_id != $user->id) {
            MessageService::abort(403, 'messages.permission.error');
        }

        if ($user->isGuest() && $booking->guest_id != $user->id) {
            MessageService::abort(403, 'messages.permission.error');
        }
    }

    public static function create($data)
    {
        $user = User::auth();

        if (!$user) {
            MessageService::abort(403, 'messages.permission.error');
        }

        if ($user->isGuest()) {
            $data['guest_id'] = $user->id;
        }

        return $data;
    }

    public static function canUpdate($booking)
    {
        $user = User::auth();

        if (!$user) {
            MessageService::abort(403, 'messages.permission.error');
        }

        if ($user->isHost() && $booking->host_id != $user->id) {
            MessageService::abort(403, 'messages.permission.error');
        }

        if ($user->isGuest() && $booking->guest_id != $user->id) {
            MessageService::abort(403, 'messages.permission.error');
        }
    }

    public static function canDelete($booking)
    {
        $user = User::auth();

        if (!$user) {
            MessageService::abort(403, 'messages.permission.error');
        }

        if ($user->isHost() && $booking->host_id != $user->id) {
            MessageService::abort(403, 'messages.permission.error');
        }

        if ($user->isGuest() && $booking->guest_id != $user->id) {
            MessageService::abort(403, 'messages.permission.error');
        }
    }
}
