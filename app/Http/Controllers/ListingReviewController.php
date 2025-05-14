<?php

namespace App\Http\Controllers;

use App\Http\Permissions\ListingReviewPermission;
use App\Http\Requests\ListingReview\CreateRequest;
use App\Http\Requests\ListingReview\UpdateRequest;
use App\Http\Resources\ListingReviewResource;
use App\Http\Services\ListingReviewService;
use App\Services\ResponseService;
use Termwind\Components\Li;

class ListingReviewController extends Controller
{
    protected $listingReviewService;

    public function __construct(ListingReviewService $listingReviewService)
    {
        $this->listingReviewService = $listingReviewService;
    }

    public function index()
    {
        $data = request()->all();
        $reviews = $this->listingReviewService->index($data);

        return ResponseService::response([
            'success' => true,
            'data'    => $reviews,
            'resource' => ListingReviewResource::class,
            'meta'    => true,
            'status'  => 200,
        ]);
    }

    public function show($id)
    {
        $review = $this->listingReviewService->show($id);

        ListingReviewPermission::canShow($review);

        return ResponseService::response([
            'success' => true,
            'data'    => $review,
            'resource' => ListingReviewResource::class,
            'status'  => 200,
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = $request->validated();

        $data = ListingReviewPermission::create($data);

        $review = $this->listingReviewService->create($data);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.listing_review.create',
            'data'    => $review,
            'resource' => ListingReviewResource::class,
            'status'  => 201,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $data = $request->validated();
        $review = $this->listingReviewService->show($id);

        ListingReviewPermission::canUpdate($review);

        $review = $this->listingReviewService->update($review, $data);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.listing_review.update',
            'data'    => $review,
            'resource' => ListingReviewResource::class,
            'status'  => 200,
        ]);
    }

    public function destroy($id)
    {
        $review = $this->listingReviewService->show($id);

        ListingReviewPermission::canDelete($review);

        $this->listingReviewService->destroy($review);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.listing_review.delete',
            'status'  => 200,
        ]);
    }
}
