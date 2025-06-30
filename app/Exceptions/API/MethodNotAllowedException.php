<?php

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;

class MethodNotAllowedException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => 'Method now allowed',
            'type' => 'method_not_allowed'
        ], 405);
    }
}
