<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Login extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $credentials = $request->validated();
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'data' => [
                    'error' => 'Invalid credentials',
                ],
            ], 401);
        }

        $token = $user->createToken('api_token');

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully',
            'data' => [
                'token' => $token->plainTextToken,
            ],
        ]);
    }
}
