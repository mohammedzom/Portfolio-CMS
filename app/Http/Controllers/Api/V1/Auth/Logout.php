<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Logout extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();

        return $this->successResponse(
            [],
            'Logged out successfully.'
        );
    }
}
