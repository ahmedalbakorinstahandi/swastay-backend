<?php

namespace App\Http\Controllers;

use App\Http\Permissions\HouseTypePermission;
use App\Http\Requests\HouseTypes\CreateRequest;
use App\Http\Requests\HouseTypes\UpdateRequest;
use App\Http\Resources\HouseTypeResource;
use App\Http\Services\HouseTypeService;
use App\Services\ResponseService;

class HouseTypeController extends Controller
{
    protected $houseTypeService;

    public function __construct(HouseTypeService $houseTypeService)
    {
        $this->houseTypeService = $houseTypeService;
    }

    public function index()
    {
        $data = request()->all();
        $houseTypes = $this->houseTypeService->index($data);

        return ResponseService::response([
            'success' => true,
            'data'    => $houseTypes,
            'resource' => HouseTypeResource::class,
            'meta'    => true,
            'status'  => 200,
        ]);
    }

    public function show($id)
    {
        $houseType = $this->houseTypeService->show($id);

        HouseTypePermission::canShow($houseType);

        return ResponseService::response([
            'success' => true,
            'data'    => $houseType,
            'resource' => HouseTypeResource::class,
            'status'  => 200,
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = $request->validated();
        $houseType = $this->houseTypeService->create($data);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.house_type.create',
            'data'    => $houseType,
            'resource' => HouseTypeResource::class,
            'status'  => 201,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $data = $request->validated();

        $houseType = $this->houseTypeService->show($id);
        HouseTypePermission::canUpdate($houseType);

        $houseType = $this->houseTypeService->update($houseType, $data);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.house_type.update',
            'data'    => $houseType,
            'resource' => HouseTypeResource::class,
            'status'  => 200,
        ]);
    }

    public function destroy($id)
    {
        $houseType = $this->houseTypeService->show($id);
        HouseTypePermission::canDelete($houseType);

        $this->houseTypeService->destroy($houseType);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.house_type.delete',
            'status'  => 200,
        ]);
    }
}
