<?php

namespace App\Exceptions\API\Auth;

use Exception;
use Illuminate\Http\JsonResponse;

class UnauthorizedException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => 'Unauthorized',
            'type' => 'unauthorized'
        ], 401);
    }
}
