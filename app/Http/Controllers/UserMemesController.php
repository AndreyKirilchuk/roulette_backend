<?php

namespace App\Http\Controllers;

use App\Http\Resources\MemeResource;
use App\Models\UserMemes;
use App\Services\MemeService;
use Illuminate\Http\Request;

class UserMemesController extends Controller
{
    public function __construct(
        private readonly MemeService $memeService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $meme = $this->memeService->spin();

        return response()->json([
            "data" => [
                "meme" => MemeResource::make($meme)
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserMemes $userMemes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserMemes $userMemes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserMemes $userMemes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserMemes $userMemes)
    {
        //
    }
}
