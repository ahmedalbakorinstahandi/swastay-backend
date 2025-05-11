<?php

namespace App\Http\Controllers;

use App\Http\Permissions\FeaturePermission;
use App\Http\Requests\Feature\CreateRequest;
use App\Http\Requests\Feature\UpdateRequest;
use App\Http\Resources\FeatureResource;
use App\Http\Services\FeatureService;
use App\Services\ResponseService;

class FeatureController extends Controller
{
    protected $featureService;

    public function __construct(FeatureService $featureService)
    {
        $this->featureService = $featureService;
    }

    public function index()
    {
        $data = request()->all();
        $features = $this->featureService->index($data);

        return ResponseService::response([
            'success' => true,
            'data'    => $features,
            'resource' => FeatureResource::class,
            'meta'    => true,
            'status'  => 200,
        ]);
    }

    public function show($id)
    {
        $feature = $this->featureService->show($id);
        FeaturePermission::canShow($feature);

        return ResponseService::response([
            'success' => true,
            'data'    => $feature,
            'resource' => FeatureResource::class,
            'status'  => 200,
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = $request->validated();
        $feature = $this->featureService->create($data);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.feature.create',
            'data'    => $feature,
            'resource' => FeatureResource::class,
            'status'  => 201,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $data = $request->validated();

        $feature = $this->featureService->show($id);
        FeaturePermission::canUpdate($feature);

        $feature = $this->featureService->update($feature, $data);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.feature.update',
            'data'    => $feature,
            'resource' => FeatureResource::class,
            'status'  => 200,
        ]);
    }

    public function destroy($id)
    {
        $feature = $this->featureService->show($id);
        FeaturePermission::canDelete($feature);

        $this->featureService->destroy($feature);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.feature.delete',
            'status'  => 200,
        ]);
    }
}
