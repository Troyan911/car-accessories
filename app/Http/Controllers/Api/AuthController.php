<?php

namespace App\Http\Controllers\Api;

use App\Enums\User\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthRequest;

class AuthController extends Controller
{
    public function __invoke(AuthRequest $request)
    {
        $data = $request->validated();

        if (! auth()->attempt($data)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Credentials',
            ], 422);
        }

        $permissions = auth()->user()
            ->hasAnyRole(Roles::ADMIN->value, Roles::MODERATOR->value) ? ['full'] : ['read'];

        $token = auth()->user()->createToken(
            $request->device_name ?? 'api',
            $permissions,
            now()->addMinutes(240)
        );

        return response()->json([
            'status' => 'success',
            'data' => [
                'token' => $token->plainTextToken,
            ],
        ]);
    }
}
