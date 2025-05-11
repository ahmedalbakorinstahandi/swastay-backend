<?php

namespace App\Http\Controllers;

use App\Http\Permissions\ListingPermission;
use App\Http\Requests\Listing\AvailableDateRequest;
use App\Http\Requests\Listing\CreateRequest;
use App\Http\Requests\Listing\UpdateRequest;
use App\Http\Resources\ListingResource;
use App\Http\Services\ListingService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Termwind\Components\Li;

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
}
