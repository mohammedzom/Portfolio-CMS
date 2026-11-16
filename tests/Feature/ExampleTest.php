<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    // Phase 2: "/" serves the Blade portfolio instead of redirecting to the external frontend.
    $this->withoutVite();
    Queue::fake();

    $response = $this->get('/');

    $response->assertOk();
});
