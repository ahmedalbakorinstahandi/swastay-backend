<?php

namespace App\Http\Permissions;

use App\Models\Booking;
use App\Models\Listing;
use App\Models\User;
use App\Services\MessageService;

class ListingReviewPermission
{

    static function filterIndex($query)
    {
        // $user = User::auth();
        // if ($user && !$user->isHost()) {
        //     $query->where('user_id', $user->id);
        // }
    }


    public static function canShow($review) {}


    public static function create($data)
    {
        $user = User::auth();


        $data['user_id'] = $user->id;

        $booking = Booking::find($data['booking_id']);

        if ($booking->guest_id !== $user->id) {
            MessageService::abort(403, 'messages.permission.error');
        }

        if ($booking->status !== 'completed') {
            MessageService::abort(403, 'messages.booking.not_completed');
        }

        $existingReview = $booking->review()->first();

        if ($existingReview) {
            MessageService::abort(403, 'messages.listing_review.already_exists');
        }


        return $data;
    }

    public static function canUpdate($review)
    {
        $user = User::auth();
        if (!$user || $review->user_id !== $user->id) {
            MessageService::abort(403, 'messages.permission.error');
        }
    }

    public static function canDelete($review)
    {
        $user = User::auth();

        if ($user && $user->isGuest() && $review->user_id !== $user->id) {
            MessageService::abort(403, 'messages.permission.error');
        }
    }
}
