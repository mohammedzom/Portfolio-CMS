<?php

namespace App\Jobs;

use App\Models\Visit;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class LogVisitJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly string $ipAddress,
        public readonly ?string $userAgent,
        public readonly string $visitedAt,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
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
        } catch (Exception $e) {
            Log::warning('Failed to log visit: '.$e->getMessage());
        }
    }
}
