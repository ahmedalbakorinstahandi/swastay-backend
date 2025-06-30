<?php

namespace App\Http\Notifications;

use App\Services\FirebaseService;

class ListingNotification
{
    public static function listingApproved($listing)
    {
        FirebaseService::sendToTopicAndStorage(
            'user-' . $listing->host_id,
            [
                $listing->host_id,
            ],
            [
                'notifiable_id' => $listing->id,
                'notifiable_type' => 'listing',
            ],
            'notifications.listing.approved.title',
            'notifications.listing.approved.body',
            [
                'listing_id' => '#' . $listing->id,
            ],
            [],
        );
    }

    public static function listingRejected($listing)
    {
        FirebaseService::sendToTopicAndStorage(
            'user-' . $listing->host_id,
            [
                $listing->host_id,
            ],
            [
                'notifiable_id' => $listing->id,
                'notifiable_type' => 'listing',
            ],
            'notifications.listing.rejected.title',
            'notifications.listing.rejected.body',
            [
                'listing_id' => '#' . $listing->id,
            ],
            [],
        );
    }

    // paused
    public static function listingPaused($listing)
    {
        FirebaseService::sendToTopicAndStorage(
            'user-' . $listing->host_id,
            [
                $listing->host_id,
            ],
            [
                'notifiable_id' => $listing->id,
                'notifiable_type' => 'listing',
            ],
            'notifications.listing.paused.title',
            'notifications.listing.paused.body',
            [
                'listing_id' => '#' . $listing->id,
            ],
            [],
        );
    }
}
