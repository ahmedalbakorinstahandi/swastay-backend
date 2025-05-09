<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\AuthServices;
use App\Services\ResponseService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authServices;

    public function __construct(AuthServices $authServices)
    {
        $this->authServices = $authServices;
    }


    public function login(LoginRequest $request)
    {
        $data = $this->authServices->login($request->validated());

        return ResponseService::response([
            'status' => 200,
            'access_token' => $data['token'],
            'message' => 'auth.login_success',
            'data' => new UserResource($data['user']),
        ]);
    }

    // register function with phone number
    public function register(RegisterRequest $request)
    {
        $data = $this->authServices->register($request->validated());

        return ResponseService::response([
            'status' => 200,
            'message' => 'auth.we_sent_verification_code_to_your_phone',
            'data' => new UserResource($data['user']),
            'info' => [
                'code_duration' => $data['minutes'],
                'otp_expire_at' => $data['otp_expire_at'],
            ],
        ]);
    }
}
