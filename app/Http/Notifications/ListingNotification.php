<?php

namespace App\Http\Notifications;

use App\Models\Listing;
use App\Models\User;
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
            'notifications.listing.host.approved.title',
            'notifications.listing.host.approved.body',
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
            'notifications.listing.host.rejected.title',
            'notifications.listing.host.rejected.body',
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
            'notifications.listing.host.paused.title',
            'notifications.listing.host.paused.body',
            [
                'listing_id' => '#' . $listing->id,
            ],
            [],
        );
    }

    public static function listingFirstCreated($listing)
    {
        $is_first_listing = Listing::where('host_id', $listing->host_id)->withTrashed()->count() === 1;

        if ($is_first_listing) {
            FirebaseService::sendToTopicAndStorage(
                'user-' . $listing->host_id,
                [
                    $listing->host_id,
                ],
                [
                    'notifiable_id' => $listing->id,
                    'notifiable_type' => 'listing',
                ],
                'notifications.listing.host.first_created.title',
                'notifications.listing.host.first_created.body',
                [
                    'listing_id' => '#' . $listing->id,
                ],
                [],
            );
        }
    }

    public static function listingCreated($listing) 
    {
        // admin
        $admin_ids = User::where('role', 'admin')->pluck('id')->toArray();
        FirebaseService::sendToTopicAndStorage(
            'role-admin',
            $admin_ids,
            [
                'notifiable_id' => $listing->id,
                'notifiable_type' => 'listing',
            ],
            'notifications.listing.admin.created.title',
            'notifications.listing.admin.created.body',
            [
                'listing_id' => '#' . $listing->id,
                'full_name' => $listing->host->first_name . ' ' . $listing->host->last_name,
            ],
            [],
        );
    }
}
