<?php

namespace App\Http\Services;

use App\Http\Notifications\ListingReviewNotification;
use App\Models\ListingReview;
use App\Models\User;
use App\Services\FilterService;
use App\Services\MessageService;

class ListingReviewService
{
    public function index($filters = [])
    {
        $query = ListingReview::with(['user', 'booking.listing']);

        if (!empty($filters['listing_id'])) {
            $listingId = $filters['listing_id'];

            $query->whereHas('booking', function ($q) use ($listingId) {
                $q->where('listing_id', $listingId);
            });
        }

        return FilterService::applyFilters(
            $query,
            $filters,
            ['comment'],
            ['rating'],
            ['created_at'],
            ['user_id', 'booking_id'],
            ['user_id']
        );
    }



    public function show($id)
    {
        $review = ListingReview::where('id', $id)->first();

        if (!$review) {
            MessageService::abort(404, 'messages.listing_review.not_found');
        }


        $review->load(['user', 'booking']);

        return $review;
    }

    public function create($data)
    {
        $review = ListingReview::create($data);

        ListingReviewNotification::guestCreated($review);


        $review->load(['user', 'booking']);

        return $review;
    }

    public function update(ListingReview $review, array $data)
    {


        if (isset($data['block'])) {

            if ($data['block'] == true) {

                $data['blocked_at'] = now();

                ListingReviewNotification::blocked($review);
            }

            if ($data['block'] == false) {
                $data['blocked_at'] = null;
            }
        }


        $review->update($data);

        $review->load(['user', 'booking']);

        return $review;
    }

    public function destroy(ListingReview $review)
    {
        $review->delete();
    }
}
