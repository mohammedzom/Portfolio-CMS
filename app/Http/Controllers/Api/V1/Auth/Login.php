<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class Login extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials)) {
            return $this->errorResponse(
                'Invalid credentials',
                401,
                [
                    'error' => 'Invalid credentials',
                ]
            );
        }

        $token = Auth::user()->createToken('api_token');

        return $this->successResponse(
            [
                'token' => $token->plainTextToken,
            ],
            'Logged in successfully.'
        );
    }
}
