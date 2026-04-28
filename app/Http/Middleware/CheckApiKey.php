<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('x-api-key');
        Log::debug('Atempting to access API From ip : '.$request->ip().' With API Key: '.$apiKey);
        if (! $apiKey || $apiKey !== config('app.api_key')) {
            throw new AccessDeniedHttpException('Unauthorized');
        }

        return $next($request);
    }
}
