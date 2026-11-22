<?php

use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Support\Facades\Process;

/*
 * .github/scripts/smoke-release.php is what deploy-release.sh runs against a
 * prepared release before switching it live. These tests run it against this
 * application in a separate PHP process with a temporary SQLite database.
 */

const SMOKE_SCRIPT = '.github/scripts/smoke-release.php';
const SMOKE_DOMAIN = 'mohammedzomlot.dev';

/**
 * @param  array<string, string>  $overrides
 * @return array<string, string>
 */
function smokeEnvironment(string $database, array $overrides = []): array
{
    return array_merge([
        'APP_ENV' => 'production',
        'APP_DEBUG' => 'false',
        'APP_KEY' => 'base64:'.base64_encode(str_repeat('k', 32)),
        'APP_URL' => 'https://'.SMOKE_DOMAIN,
        'DB_CONNECTION' => 'sqlite',
        'DB_URL' => '',
        'DB_DATABASE' => $database,
        'CACHE_STORE' => 'array',
        'SESSION_DRIVER' => 'array',
        'QUEUE_CONNECTION' => 'sync',
        'LOG_CHANNEL' => 'null',
        'API_KEY' => 'smoke-test-key',
    ], $overrides);
}

afterEach(function () {
    foreach ($GLOBALS['smokeDatabases'] ?? [] as $database) {
        @unlink($database);
    }
    $GLOBALS['smokeDatabases'] = [];
});

function migratedDatabase(bool $withSiteSettings = true): string
{
    $database = sys_get_temp_dir().'/portfolio-smoke-'.bin2hex(random_bytes(6)).'.sqlite';
    touch($database);
    $GLOBALS['smokeDatabases'][] = $database;

    Process::path(base_path())
        ->env(smokeEnvironment($database))
        ->run([PHP_BINARY, 'artisan', 'migrate', '--force', '--no-interaction'])
        ->throw();

    if ($withSiteSettings) {
        (new PDO('sqlite:'.$database))->exec('INSERT INTO site_settings DEFAULT VALUES');
    }

    return $database;
}

function runSmoke(string $database, string $mode = 'auto', array $env = []): ProcessResult
{
    return Process::path(base_path())
        ->env(smokeEnvironment($database, $env))
        ->run([PHP_BINARY, base_path(SMOKE_SCRIPT), base_path(), SMOKE_DOMAIN, 'https', $mode]);
}

it('passes and checks the legacy API when the route and API_KEY exist', function () {
    $result = runSmoke(migratedDatabase());

    expect($result->exitCode())->toBe(0, $result->errorOutput())
        ->and($result->output())
        ->toContain('/up -> 200')
        ->toContain('database -> ok')
        ->toContain('/ -> 200')
        ->toContain('/api/v1/portfolio -> 200 success=true')
        ->not->toContain('smoke-test-key');
});

it('checks the live cache store without leaving application entries in it', function () {
    $database = migratedDatabase();

    $result = runSmoke($database, 'auto', ['CACHE_STORE' => 'database']);

    // Only rate-limiter counters (expiring within a minute) may remain; data built
    // by the new code, such as portfolio_all, must never reach the live store.
    $keys = (new PDO('sqlite:'.$database))->query('select key from cache')->fetchAll(PDO::FETCH_COLUMN);

    expect($result->exitCode())->toBe(0, $result->errorOutput())
        ->and($result->output())->toContain('cache (database) -> ok')
        ->and(array_filter($keys, fn (string $key): bool => str_contains($key, 'portfolio')))->toBe([])
        ->and(array_filter($keys, fn (string $key): bool => str_contains($key, 'portfolio-deploy-smoke')))->toBe([]);
});

it('fails the release when the legacy API check fails in auto mode', function () {
    $result = runSmoke(migratedDatabase(withSiteSettings: false));

    expect($result->exitCode())->toBe(1)
        ->and($result->errorOutput())->toContain('/api/v1/portfolio -> 404');
});

it('skips the legacy API check in auto mode when API_KEY is not configured', function () {
    $result = runSmoke(migratedDatabase(withSiteSettings: false), 'auto', ['API_KEY' => '']);

    expect($result->exitCode())->toBe(0, $result->errorOutput())
        ->and($result->output())->toContain('/api/v1/portfolio skipped (API_KEY is not configured)');
});

it('never checks the legacy API in skip mode', function () {
    $result = runSmoke(migratedDatabase(withSiteSettings: false), 'skip');

    expect($result->exitCode())->toBe(0, $result->errorOutput())
        ->and($result->output())->toContain('skipped (PORTFOLIO_HEALTH_API=skip)');
});

it('fails in require mode when the legacy API cannot be checked', function () {
    $result = runSmoke(migratedDatabase(), 'require', ['API_KEY' => '']);

    expect($result->exitCode())->toBe(1)
        ->and($result->errorOutput())->toContain('required, but API_KEY is not configured');
});

it('fails when the database is unreachable', function () {
    $result = runSmoke(sys_get_temp_dir().'/missing-dir-'.uniqid().'/database.sqlite', 'skip');

    expect($result->exitCode())->toBe(1)
        ->and($result->errorOutput())->toContain('database FAILED');
});

it('rejects an unknown legacy API mode', function () {
    $result = runSmoke(migratedDatabase(), 'sometimes');

    expect($result->exitCode())->toBe(2);
});

/**
 * @param  array<string, array{0: int, 1: string}>  $responses
 */
function followWith(array $responses): array
{
    require_once base_path(SMOKE_SCRIPT);

    return portfolio_smoke_follow(fn (string $path): array => $responses[$path] ?? [404, ''], SMOKE_DOMAIN, '/');
}

it('decides whether the home page is healthy', function (array $responses, bool $healthy) {
    expect(followWith($responses)['ok'])->toBe($healthy);
})->with([
    'direct 200' => [['/' => [200, '']], true],
    'same-host redirect then 200' => [['/' => [302, '/home'], '/home' => [200, '']], true],
    'absolute same-host redirect then 200' => [['/' => [301, 'https://'.SMOKE_DOMAIN.'/en?x=1'], '/en?x=1' => [200, '']], true],
    'redirect to itself (loop)' => [['/' => [302, 'https://'.SMOKE_DOMAIN]], false],
    'redirect loop between two paths' => [['/' => [302, '/a'], '/a' => [302, '/']], false],
    'redirect to another host' => [['/' => [302, 'https://old-api.example.com/']], false],
    'not found' => [['/' => [404, '']], false],
    'server error' => [['/' => [500, '']], false],
    'redirect without Location' => [['/' => [302, '']], false],
    'too many redirects' => [[
        '/' => [302, '/1'], '/1' => [302, '/2'], '/2' => [302, '/3'],
        '/3' => [302, '/4'], '/4' => [302, '/5'], '/5' => [302, '/6'], '/6' => [200, ''],
    ], false],
]);
