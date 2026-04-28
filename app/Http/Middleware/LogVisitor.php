<?php

namespace App\Http\Middleware;

use App\Jobs\LogVisitJob;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $ipAddress = $request->ip();

        if ($response->status() === 200 && $ipAddress !== null && ! $this->isBot($request->userAgent())) {
            dispatch(new LogVisitJob(
                $ipAddress,
                $request->userAgent(),
                now()->toDateString(),
            ));
        }

        return $response;
    }

    private function isBot(?string $userAgent): bool
    {
        if (empty($userAgent)) {
            return false;
        }

        $bots = [
            'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider', 'yandexbot',
            'crawler', 'spider', 'curl', 'postman', 'guzzle', 'headless',
        ];

        foreach ($bots as $bot) {
            if (str_contains(strtolower($userAgent), $bot)) {
                return true;
            }
        }

        return false;
    }
}
