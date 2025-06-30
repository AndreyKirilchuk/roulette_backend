<?php

namespace App\Exceptions\Api\Auth;

use Exception;
use Illuminate\Http\JsonResponse;

class InvalidRefreshTokenException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => 'Invalid refresh token',
            'type' => 'invalid_refresh_token'
        ], 400);
    }
}
