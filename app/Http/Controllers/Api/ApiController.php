<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    protected function can(string $ability)
    {
        $condition = auth()->user()->tokenCan($ability) || auth()->user()->tokenCan('full');

        if (! $condition) {
            return response()->json([
                'status' => 'error',
                'message' => 'access denied',
            ], 403);
        }
    }
}
