<?php

namespace App\Http\Services;

use App\Models\ListingReview;
use App\Services\FilterService;
use App\Services\MessageService;

class ListingReviewService
{
    public function index($filters = [])
    {

        $query = ListingReview::query()->with(['user', 'booking']);


        $listingId = $filters['listing_id'] ?? null;

        if ($listingId) {
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
            ['user_id', 'booking_id', 'booking.listing_id'],
            ['user_id']
        );
    }

    public function show($id)
    {
        $review = ListingReview::where('id', $id)->first();

        if (!$review) {
            MessageService::abort(404, 'messages.listing_review.not_found');
        }

        $review = $review->with(['user', 'booking'])->first();

        return $review;
    }

    public function create($data)
    {
        $review = ListingReview::create($data);


        $review->load(['user', 'booking']);

        return $review;
    }

    public function update(ListingReview $review, array $data)
    {


        if (isset($data['block'])) {
            $data['blocked_at'] = now();
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
