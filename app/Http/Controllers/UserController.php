<?php

namespace App\Http\Controllers;

use App\Http\Permissions\UserPermission;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\UserService;
use App\Models\User;
use App\Services\ResponseService;
use Illuminate\Database\Eloquent\Model;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function index()
    {
        $users = $this->userService->index(request()->all());

        return ResponseService::response([
            'success' => true,
            'data'    => $users,
            'resource' => UserResource::class,
            'meta'    => true,
            'status'  => 200,
        ]);
    }

    public function show($id)
    {
        $user = $this->userService->show($id);

        UserPermission::canShow($user);

        return ResponseService::response([
            'success' => true,
            'data'    => $user,
            'resource' => UserResource::class,
            'status'  => 200,
        ]);
    }

    public function create(CreateRequest $request)
    {
        $data = $request->validated();

        $user = $this->userService->create($data);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.user.create',
            'data'    => $user,
            'resource' => UserResource::class,
            'status'  => 201,
        ]);
    }

    public function update(UpdateRequest $request, $id)
    {
        $data = $request->validated();

        $user = $this->userService->show($id);

        UserPermission::canUpdate($user);

        $user = $this->userService->update($user, $data);


        return ResponseService::response([
            'success' => true,
            'message' => 'messages.user.update',
            'data'    => $user,
            'resource' => UserResource::class,
            'status'  => 200,
        ]);
    }

    public function destroy($id)
    {
        $user = $this->userService->show($id);

        UserPermission::canDelete($user);

        $this->userService->destroy($user);

        return ResponseService::response([
            'success' => true,
            'message' => 'messages.user.delete',
            'status'  => 200,
        ]);
    }


    public function getProfile()
    {
        $user = User::auth();

        return ResponseService::response([
            'success' => true,
            'data'    => $user,
            'resource' => UserResource::class,
            'status'  => 200,
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $data = $request->validated();

        $user = User::auth();

        $user = $this->userService->updateProfile($user, $data);


        return ResponseService::response([
            'success' => true,
            'message' => 'messages.user.update',
            'data'    => $user,
            'resource' => UserResource::class,
            'status'  => 200,
        ]);
    }
}
