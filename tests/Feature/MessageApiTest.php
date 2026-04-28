<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('validates contact form payloads', function () {
    $this->postJson('/api/v1/message', [])
        ->assertUnprocessable()
        ->assertJsonStructure([
            'data' => [
                'name', 'email', 'subject', 'body',
            ],
        ]);
});

it('rate limits contact form submissions', function () {
    $payload = [
        'name' => 'Rate Limited',
        'email' => 'rate@example.com',
        'subject' => 'Hello',
        'body' => 'A valid message body.',
    ];

    for ($attempt = 0; $attempt < 3; $attempt++) {
        $this->postJson('/api/v1/message', $payload)->assertCreated();
    }

    $this->postJson('/api/v1/message', $payload)
        ->assertTooManyRequests();
});
