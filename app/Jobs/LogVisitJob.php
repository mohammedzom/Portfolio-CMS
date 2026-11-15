<?php

namespace App\Jobs;

use App\Models\Visit;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class LogVisitJob implements ShouldQueue
{
    use Queueable;

    /**
     * The upsert is idempotent, so retrying a transient failure is safe.
     */
    public int $tries = 3;

    public int $timeout = 30;

    /**
     * Seconds to wait before each retry.
     *
     * @var list<int>
     */
    public array $backoff = [10, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly string $ipAddress,
        public readonly ?string $userAgent,
        public readonly string $visitedAt,
    ) {}

    /**
     * Record one visit per IP address and day. Exceptions are not caught so
     * the queue can retry the job and record it in failed_jobs.
     */
    public function handle(): void
    {
        $timestamp = CarbonImmutable::now();

        Visit::query()->upsert([
            [
                'ip_address' => $this->ipAddress,
                'visited_at' => $this->visitedAt,
                'user_agent' => $this->userAgent ? substr($this->userAgent, 0, 255) : null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ], ['ip_address', 'visited_at'], ['user_agent', 'updated_at']);
    }

    /**
     * Report a visit that could not be recorded after every retry. The IP
     * address is intentionally left out of the log entry.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('Failed to record a portfolio visit after all retries.', [
            'visited_at' => $this->visitedAt,
            'exception' => $exception?->getMessage(),
        ]);
    }
}
