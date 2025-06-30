<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\AuthTelegramRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function login(LoginRequest $request)
    {
        $requestData = $request->validated();

        $user = $this->authService->login($requestData);

        return response()->json([
            "data" => [
                "user" => UserResource::make($user),
            ]
        ])
        ->withCookie(cookie("access_token", $user["access_token"], config('jwt.ttl')))
        ->withCookie(cookie("refresh_token", $user["refresh_token"], 43200));
    }

    public function register(RegisterRequest $request)
    {
        $requestData = $request->validated();

        $user = $this->authService->register($requestData);

        return response()->json([
            "data" => [
                "user" => UserResource::make($user),
            ]
        ], 201)
        ->withCookie(cookie("access_token", $user["access_token"], config('jwt.ttl')))
        ->withCookie(cookie("refresh_token", $user["refresh_token"], 43200));
    }

    public function refresh(Request $request)
    {
        $refresh_token = $request->cookie('refresh_token');

        $access_token = $this->authService->refresh($refresh_token);

        return response()->json([
            "message" => "Token refreshed"
        ])->withCookie(cookie('access_token', $access_token, 60));
    }

    public function get()
    {
        return response()->json([
           "message" => "success"
        ]);
    }
}
