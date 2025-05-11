<?php

namespace App\Http\Permissions;

use App\Models\User;
use App\Services\MessageService;
use Illuminate\Support\Facades\Auth;

class ListingPermission
{
    public static function filterIndex($query)
    {

        $is_guest = false;

        $user = User::auth();
        if ($user) {


            if ($user->isHost()) {
                $query->where('host_id', $user->id);
            } elseif ($user->isGuest()) {
                $is_guest = true;
            }
        } else {
            $is_guest = true;
        }

        if ($is_guest) {
            $query->where('status', 'approved')->where('is_published', true);
        }

        return $query;
    }

    public static function canShow($listing)
    {


        $canShow = false;


        $user = User::auth();

        if ($user) {

            if ($user->isHost()) {
                $canShow = $listing->host_id == $user->id;
            } elseif ($user->isGuest()) {
                $canShow = $listing->status == 'approved' && $listing->is_published;
            } elseif ($user->isAdmin()) {
                $canShow = true;
            }
        }

        abort(
            response()->json([
                'success' => false,
                'data'    => $user,
                'canShow' => $canShow,
                'isAdmin' => $user->isAdmin(),
                'isHost'  => $user->isHost(),
                'isGuest' => $user->isGuest(),
            ], 403)
        );



        if (!$canShow) {
            MessageService::abort(403, 'messages.permission.error');
        }

        return false;
    }

    public static function create($data)
    {
        $user = User::auth();


        if ($user->isHost()) {
            $data['host_id'] = $user->id;
        }

        $host = User::find($data['host_id']);

        if (!$host || !$host->isHost()) {
            MessageService::abort(403, 'messages.host.not_found');
        }

        return $data;
    }

    public static function canUpdate($listing)
    {
        $user = User::auth();

        if ($user->isHost()) {
            if ($listing->host_id != $user->id) {
                MessageService::abort(403, 'messages.permission.error');
            }
        }

        return false;
    }

    public static function canDelete($listing)
    {
        $user = User::auth();

        if ($user->isHost()) {
            if ($listing->host_id != $user->id) {
                MessageService::abort(403, 'messages.permission.error');
            }
        }

        return false;
    }
}
