<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class Login extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return $this->errorResponse(
                'Invalid credentials',
                401,
                [
                    'error' => 'Invalid credentials',
                ]
            );
        }

        $token = $user->createToken('api_token');

        return $this->successResponse(
            [
                'token' => $token->plainTextToken,
            ],
            'Logged in successfully.'
        );
    }
}
