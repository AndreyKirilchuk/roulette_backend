<?php

namespace App\Exceptions\API;

use Exception;
use Illuminate\Http\JsonResponse;

class ResourceNotFoundException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => 'Resource not found',
            'type' => 'resource_not_found'
        ], 404);
    }
}
