<?php

/*
 * In-process smoke test of a prepared release, run by deploy-release.sh
 * before the release is switched live. Requests go through the release's own
 * HTTP kernel with the production configuration, so nothing is served yet.
 *
 * Usage: php smoke-release.php <release-dir> <domain> <https|http> [auto|require|skip]
 *
 * Checks:
 *   GET /up       must return 200
 *   database      the default connection must answer `select 1`
 *   GET /         must end in 200 on <domain>; same-host redirects are followed
 *                 (at most 5), a loop or a redirect to another host fails
 *   GET /api/v1/portfolio (legacy REST API), depending on the mode:
 *       auto      checked when the release registers the route and API_KEY is set
 *       require   checked, and fails if the route or API_KEY is missing
 *       skip      not checked
 *
 * It exits 0 when every check passes and 1 otherwise. Secrets are never printed.
 */

if (! function_exists('portfolio_smoke_follow')) {
    /**
     * Follows same-host redirects for a path and decides whether it is healthy.
     *
     * @param  callable(string): array{0: int, 1: string}  $get  returns [status, Location header]
     * @return array{ok: bool, lines: list<string>}
     */
    function portfolio_smoke_follow(callable $get, string $domain, string $path, int $maxRedirects = 5): array
    {
        $lines = [];
        $seen = [];

        for ($hop = 0; $hop <= $maxRedirects; $hop++) {
            if (in_array($path, $seen, true)) {
                $lines[] = "  redirect loop on $domain at $path";

                return ['ok' => false, 'lines' => $lines];
            }
            $seen[] = $path;

            [$status, $location] = $get($path);
            $lines[] = sprintf('  %s -> %d%s', $path, $status, $location !== '' ? ' Location: '.$location : '');

            if ($status === 200) {
                return ['ok' => true, 'lines' => $lines];
            }
            if ($status < 300 || $status >= 400 || $location === '') {
                return ['ok' => false, 'lines' => $lines];
            }

            $target = parse_url($location);
            $host = strtolower($target['host'] ?? $domain);
            if ($host !== strtolower($domain)) {
                $lines[] = "  $path redirects away from $domain (to $host)";

                return ['ok' => false, 'lines' => $lines];
            }
            $next = ($target['path'] ?? '') === '' ? '/' : $target['path'];
            $path = isset($target['query']) ? $next.'?'.$target['query'] : $next;
        }

        $lines[] = "  more than $maxRedirects redirects on $domain";

        return ['ok' => false, 'lines' => $lines];
    }
}

if (PHP_SAPI !== 'cli' || realpath($_SERVER['SCRIPT_FILENAME'] ?? '') !== __FILE__) {
    return; // Loaded by a test for portfolio_smoke_follow().
}

[$base, $domain, $scheme, $apiMode] = array_pad(array_slice($argv, 1), 4, null);
$scheme = $scheme ?: 'https';
$apiMode = $apiMode ?: 'auto';
if (! is_string($base) || ! is_dir($base) || ! is_string($domain) || $domain === '' || ! in_array($apiMode, ['auto', 'require', 'skip'], true)) {
    fwrite(STDERR, "usage: php smoke-release.php <release-dir> <domain> <https|http> [auto|require|skip]\n");
    exit(2);
}

require $base.'/vendor/autoload.php';
$app = require $base.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = function (string $path, array $headers = []) use ($kernel, $domain, $scheme): Symfony\Component\HttpFoundation\Response {
    $server = [
        'HTTP_HOST' => $domain,
        'HTTPS' => $scheme === 'https' ? 'on' : 'off',
        'SERVER_PORT' => $scheme === 'https' ? 443 : 80,
        'REMOTE_ADDR' => '127.0.0.1',
        // "curl" keeps the request out of visit tracking (LogVisitor treats it as a bot).
        'HTTP_USER_AGENT' => 'portfolio-deploy-healthcheck curl',
        'HTTP_ACCEPT' => 'text/html,application/json',
    ];
    foreach ($headers as $name => $value) {
        $server['HTTP_'.strtoupper(str_replace('-', '_', $name))] = $value;
    }
    $req = Illuminate\Http\Request::create($scheme.'://'.$domain.$path, 'GET', [], [], [], $server);
    $res = $kernel->handle($req);
    $kernel->terminate($req, $res);

    return $res;
};

$failed = false;

$up = $request('/up');
printf("  /up -> %d\n", $up->getStatusCode());
$failed = $failed || $up->getStatusCode() !== 200;

try {
    Illuminate\Support\Facades\DB::connection()->select('select 1');
    echo "  database -> ok\n";
} catch (Throwable $e) {
    fwrite(STDERR, '  database FAILED -> '.get_class($e)."\n");
    $failed = true;
}

$home = portfolio_smoke_follow(function (string $path) use ($request): array {
    $res = $request($path);

    return [$res->getStatusCode(), (string) $res->headers->get('Location', '')];
}, $domain, '/');
fwrite($home['ok'] ? STDOUT : STDERR, implode("\n", $home['lines'])."\n");
$failed = $failed || ! $home['ok'];

$hasRoute = false;
foreach ($app['router']->getRoutes()->getRoutes() as $route) {
    if ($route->uri() === 'api/v1/portfolio' && in_array('GET', $route->methods(), true)) {
        $hasRoute = true;
        break;
    }
}
$key = (string) config('app.api_key', '');

if ($apiMode === 'skip') {
    echo "  /api/v1/portfolio skipped (PORTFOLIO_HEALTH_API=skip)\n";
} elseif ($hasRoute && $key !== '') {
    $api = $request('/api/v1/portfolio', ['X-Api-Key' => $key, 'Accept' => 'application/json']);
    $json = json_decode((string) $api->getContent(), true);
    $ok = $api->getStatusCode() === 200 && ($json['success'] ?? false) === true;
    fwrite($ok ? STDOUT : STDERR, sprintf("  /api/v1/portfolio -> %d success=%s\n", $api->getStatusCode(), var_export($json['success'] ?? null, true)));
    $failed = $failed || ! $ok;
} elseif ($apiMode === 'require') {
    fwrite(STDERR, '  /api/v1/portfolio FAILED -> required, but '.($hasRoute ? 'API_KEY is not configured' : 'the route is not registered')."\n");
    $failed = true;
} else {
    echo '  /api/v1/portfolio skipped ('.($hasRoute ? 'API_KEY is not configured' : 'route not registered by this release').")\n";
}

exit($failed ? 1 : 0);
