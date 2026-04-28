<?php

use App\Jobs\LogVisitJob;
use App\Models\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('dispatches visit logging instead of writing in middleware', function () {
    Queue::fake();
    SiteSettings::create();

    $this->getJson('/api/v1/portfolio')
        ->assertSuccessful();

    Queue::assertPushed(LogVisitJob::class);
});

it('handles requests with a null user agent', function () {
    Queue::fake();
    SiteSettings::create();

    $this->withHeaders(['User-Agent' => null])
        ->getJson('/api/v1/portfolio')
        ->assertSuccessful();

    Queue::assertPushed(LogVisitJob::class, fn (LogVisitJob $job): bool => $job->userAgent === null);
});
