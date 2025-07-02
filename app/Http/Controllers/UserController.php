<?php

namespace App\Http\Controllers;

use App\Http\Resources\MemeResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UsersResource;
use App\Models\Meme;
use App\Services\MemeService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly MemeService $memeService
    ) {}

    public function profile()
    {
        $user = auth()->user();

        $memes = $this->memeService->getUserMemes($user);

        return response()->json([
           "data" => [
               "user" => UserResource::make($user),
               "memes" => MemeResource::collection($memes)
           ]
        ]);
    }

    public function index(Request $request)
    {
        $users = $this->userService->users($request);

        return response()->json([
            "data" => [
                "users" => UserResource::collection($users)
            ]
        ]);
    }
}
