<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class Login extends Controller
{
    /**
     * Issue an API token without logging the user into the web session guard,
     * so token-based API access stays independent from session-based web auth.
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $guard = Auth::guard('web');

        if (! $guard->validate($credentials)) {
            return $this->errorResponse(
                'Invalid credentials',
                401,
                [
                    'error' => 'Invalid credentials',
                ]
            );
        }

        $token = $guard->getLastAttempted()->createToken('api_token');

        return $this->successResponse(
            [
                'token' => $token->plainTextToken,
            ],
            'Logged in successfully.'
        );
    }
}
