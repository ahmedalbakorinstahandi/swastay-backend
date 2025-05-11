<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListingRule\CreateRequest;
use App\Http\Requests\ListingRule\UpdateRequest;
use App\Http\Resources\ListingRuleResource;
use App\Http\Services\ListingRuleService;
use App\Services\ResponseService;

class ListingRuleController extends Controller
{
    protected $listingRuleService;

    public function __construct(ListingRuleService $listingRuleService)
    {
        $this->listingRuleService = $listingRuleService;
    }

    public function show($id)
    {
        $rule = $this->listingRuleService->show($id);

        return ResponseService::response([
            'success' => true,
            'data' => $rule,
            'resource' => ListingRuleResource::class,
            'status' => 200,
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = $request->validated();
        $rule = $this->listingRuleService->create($data);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.listing_rule.create',
            'data' => $rule,
            'resource' => ListingRuleResource::class,
            'status' => 201,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $data = $request->validated();
        $rule = $this->listingRuleService->show($id);
        $rule = $this->listingRuleService->update($rule, $data);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.listing_rule.update',
            'data' => $rule,
            'resource' => ListingRuleResource::class,
            'status' => 200,
        ]);
    }

    public function destroy($id)
    {
        $rule = $this->listingRuleService->show($id);
        $this->listingRuleService->destroy($rule);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.listing_rule.delete',
            'status' => 200,
        ]);
    }
}
