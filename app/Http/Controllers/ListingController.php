<?php

namespace App\Http\Controllers;

use App\Http\Permissions\ListingPermission;
use App\Http\Requests\Listing\CreateRequest;
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

        ListingPermission::filterIndex($listings, $data);

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

    public function show(string $id)
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


    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
