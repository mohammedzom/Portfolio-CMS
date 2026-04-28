<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->status() === 200 && ! $this->isBot($request->userAgent())) {
            try {
                Visit::updateOrCreate([
                    'ip_address' => $request->ip(),
                    'visited_at' => now()->toDateString(),
                ], [
                    'user_agent' => $request->userAgent() ? substr($request->userAgent(), 0, 255) : null,
                ]);
            } catch (Exception $e) {
                Log::warning('Failed to log visit: '.$e->getMessage());
            }
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
