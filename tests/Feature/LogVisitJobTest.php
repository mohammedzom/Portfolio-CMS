<?php

use App\Jobs\LogVisitJob;
use App\Models\Visit;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

uses(LazilyRefreshDatabase::class);

it('records one visit per ip address and day', function () {
    (new LogVisitJob('203.0.113.7', 'Browser/1.0', '2026-11-15'))->handle();
    (new LogVisitJob('203.0.113.7', 'Browser/2.0', '2026-11-15'))->handle();
    (new LogVisitJob('203.0.113.7', 'Browser/2.0', '2026-11-16'))->handle();

    expect(Visit::count())->toBe(2)
        ->and(Visit::whereDate('visited_at', '2026-11-15')->sole()->user_agent)->toBe('Browser/2.0');
});

it('truncates long user agents', function () {
    (new LogVisitJob('203.0.113.7', str_repeat('a', 300), '2026-11-15'))->handle();

    expect(strlen(Visit::sole()->user_agent))->toBe(255);
});

it('lets failures reach the queue so the job is retried', function () {
    Schema::drop('visits');

    expect(fn () => (new LogVisitJob('203.0.113.7', 'Browser/1.0', '2026-11-15'))->handle())
        ->toThrow(QueryException::class);
});

it('retries with backoff before giving up', function () {
    $job = new LogVisitJob('203.0.113.7', null, '2026-11-15');

    expect($job->tries)->toBe(3)
        ->and($job->backoff)->toBe([10, 60]);
});

it('reports a permanently failed visit without logging the ip address', function () {
    Log::spy();

    (new LogVisitJob('203.0.113.7', null, '2026-11-15'))->failed(new RuntimeException('database is down'));

    Log::shouldHaveReceived('error')->once()->withArgs(
        fn (string $message, array $context): bool => $context === ['visited_at' => '2026-11-15', 'exception' => 'database is down']
            && ! str_contains($message.json_encode($context), '203.0.113.7')
    );
});
