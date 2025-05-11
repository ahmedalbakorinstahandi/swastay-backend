<?php

namespace App\Http\Controllers;

use App\Http\Permissions\CategoryPermission;
use App\Http\Requests\Categories\CreateRequest;
use App\Http\Requests\Categories\UpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Services\CategoryService;
use App\Services\ResponseService;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->index(request()->all());

        return ResponseService::response([
            'success' => true,
            'data'    => $categories,
            'resource' => CategoryResource::class,
            'status'  => 200,
        ]);
    }


    public function show($id)
    {
        $category = $this->categoryService->show($id);

        CategoryPermission::canShow($category);

        return ResponseService::response([
            'success' => true,
            'data'    => $category,
            'resource' => CategoryResource::class,
            'status'  => 200,
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = $request->validated();
        $category = $this->categoryService->create($data);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.category.create',
            'data'    => $category,
            'resource' => CategoryResource::class,
            'status'  => 201,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $data = $request->validated();

        $category = $this->categoryService->show($id);
        CategoryPermission::canUpdate($category);

        $category = $this->categoryService->update($category, $data);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.category.update',
            'data'    => $category,
            'resource' => CategoryResource::class,
            'status'  => 200,
        ]);
    }

    public function destroy($id)
    {
        $category = $this->categoryService->show($id);

        CategoryPermission::canDelete($category);

        $this->categoryService->destroy($category);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.category.delete',
            'status'  => 200,
        ]);
    }
}
