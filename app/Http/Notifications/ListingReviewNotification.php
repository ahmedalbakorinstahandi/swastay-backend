<?php

namespace App\Http\Notifications;

use App\Services\FirebaseService;

class ListingReviewNotification
{
    public static function blocked($review)
    {
        FirebaseService::sendToTopicAndStorage(
            'user-' . $review->user->id,
            [
                $review->user->id,
            ],
            [
                'notifiable_id' => $review->booking_id,
                'notifiable_type' => 'booking',
            ],
            'notifications.listing_review.guest.blocked.title',
            'notifications.listing_review.guest.blocked.body',
            [
                'guest_first_name' => $review->user->first_name,
                'review_id' => '#' . $review->id,
                'listing_id' => '#' . $review->booking->listing_id,
            ],
            [],
        );

        $host = $review->booking->listing->host;

        FirebaseService::sendToTopicAndStorage(
            'user-' . $host->id,
            [
                $host->id,
            ],
            [
                'notifiable_id' => $review->booking_id,
                'notifiable_type' => 'booking',
            ],
            'notifications.listing_review.host.blocked.title',
            'notifications.listing_review.host.blocked.body',
            [
                'host_first_name' => $host->first_name,
                'review_id' => '#' . $review->id,
                'guest_first_name' => $review->user->first_name,
                'listing_id' => '#' . $review->booking->listing_id,
            ],
            [],
        );
    }


    public static function guestCreated($review)
    {
        FirebaseService::sendToTopicAndStorage(
            'user-' . $review->booking->listing->host_id,
            [
                $review->booking->listing->host_id,
            ],
            [
                'notifiable_id' => $review->booking_id,
                'notifiable_type' => 'booking',
            ],
            'notifications.listing_review.host.created.title',
            'notifications.listing_review.host.created.body',
            [
                'host_first_name' => $review->booking->listing->host->first_name,
                'review_id' => '#' . $review->id,
                'listing_id' => '#' . $review->booking->listing_id,
            ],
            [],
        );
    }
}
