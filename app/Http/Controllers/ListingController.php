<?php

namespace App\Http\Controllers;

use App\Http\Permissions\ListingPermission;
use App\Http\Requests\Listing\AvailableDateRequest;
use App\Http\Requests\Listing\CreateRequest;
use App\Http\Requests\Listing\ReOrderListingImagesRequest;
use App\Http\Requests\Listing\ReOrderListingRequest;
use App\Http\Requests\Listing\UpdateRequest;
use App\Http\Requests\ListingRule\UpdateRequest as ListingRuleUpdateRequest;
use App\Http\Resources\ImageResource;
use App\Http\Resources\ListingResource;
use App\Http\Services\ListingService;
use App\Models\User;
use App\Services\OrderHelper;
use App\Services\ResponseService;

class ListingController extends Controller
{

    protected $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    public function index()
    {
        $data = request()->all();

        $listings = $this->listingService->index($data);

        ListingPermission::filterIndex($listings);

        return ResponseService::response(
            [
                'success' => true,
                'data'    => $listings,
                'resource' => ListingResource::class,
                'meta'    => true,
                'status'  => 200,
            ]
        );
    }

    public function show($id)
    {
        $listing = $this->listingService->show($id);

        ListingPermission::canShow($listing);

        return ResponseService::response(
            [
                'success' => true,
                'data'    => $listing,
                'resource' => ListingResource::class,
                'status'  => 200,
            ]
        );
    }


    public function create(CreateRequest $request)
    {
        $data = $request->validated();

        $data =  ListingPermission::create($data);

        $listing = $this->listingService->create($data);

        return ResponseService::response(
            [
                'success' => true,
                'message' => 'messages.listing.create',
                'data'    => $listing,
                'resource' => ListingResource::class,
                'status'  => 201,
            ]
        );
    }


    public function update(UpdateRequest $request,   $id)
    {
        $data = $request->all();

        $listing = $this->listingService->show($id);

        ListingPermission::canUpdate($listing);


        $listing = $this->listingService->update($listing, $data);

        return ResponseService::response(
            [
                'success' => true,
                'message' => 'messages.listing.update',
                'data'    => $listing,
                'resource' => ListingResource::class,
                'status'  => 200,
            ]
        );
    }


    public function destroy($id)
    {
        $listing = $this->listingService->show($id);

        ListingPermission::canDelete($listing);

        $this->listingService->destroy($listing);

        return ResponseService::response(
            [
                'success' => true,
                'message' => 'messages.listing.delete',
                'status'  => 200,
            ]
        );
    }


    public function updateAvailableDate($id, AvailableDateRequest $request)
    {
        $listing = $this->listingService->show($id);

        ListingPermission::canUpdate($listing);

        $data = $request->all();

        $listing = $this->listingService->updateAvailableDate($listing, $data);

        return ResponseService::response(
            [
                'success' => true,
                'message' => 'messages.listing.update',
                'data'    => $listing,
                'resource' => ListingResource::class,
                'status'  => 200,
            ]
        );
    }

    public function updateRule($id, ListingRuleUpdateRequest $request)
    {
        $listing = $this->listingService->show($id);

        ListingPermission::canUpdate($listing);

        $data = $request->all();

        $listing = $this->listingService->updateRule($listing, $data);

        return ResponseService::response(
            [
                'success' => true,
                'message' => 'messages.listing.update',
                'data'    => $listing,
                'resource' => ListingResource::class,
                'status'  => 200,
            ]
        );
    }


    public function listingFavoritesUpdate($id)
    {

        $listing = $this->listingService->show($id);

        ListingPermission::canShow($listing);

        $user = User::auth();

        if (!$user->favorites()->where('listing_id', $listing->id)->exists()) {
            $user->favorites()->create(
                [
                    'listing_id' => $listing->id,
                    'created_at' => now(),
                ]
            );
        } else {
            $user->favorites()->where('listing_id', $listing->id)->delete();
        }


        $listing = $this->listingService->show($id);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.user.favorite',
            'data'    => $listing,
            'resource' => ListingResource::class,
            'status'  => 200,
        ]);
    }

    // Reorder Listing
    public function reorderListing($id, ReOrderListingRequest $request)
    {
        $listing = $this->listingService->show($id);

        ListingPermission::canUpdate($listing);

        $data = $request->validated();

        $listing = $this->listingService->reorderListing($listing, $data);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.listing.reorder',
            'data'    => $listing,
            'resource' => ListingResource::class,
            'status'  => 200,
        ]);
    }

    public function reorderImage($id, $image_id, ReOrderListingImagesRequest $request)
    {
        $listing = $this->listingService->show($id);

        ListingPermission::canUpdate($listing);

        $data = $request->validated();

        $image = $listing->images()->find($image_id);

        if (!$image) {
            return ResponseService::response([
                'success' => false,
                'message' => 'messages.image.not_found',
                'status' => 404,
            ]);
        }

        OrderHelper::reorder($image, $data['orders']);

        $images = $listing->images()->get();

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.image.reorder',
            'data' => $images,
            'resource' => ImageResource::class,
        ]);
    }
}
