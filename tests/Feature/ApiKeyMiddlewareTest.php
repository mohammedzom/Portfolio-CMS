<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('rejects requests without an api key', function () {
    $this->withoutApiKey()
        ->getJson('/api/v1/portfolio')
        ->assertUnauthorized()
        ->assertJson([
            'success' => false,
            'message' => 'Invalid API key.',
        ]);
});

it('rejects requests with a wrong api key', function () {
    $this->withHeaders(['x-api-key' => 'wrong-key'])
        ->getJson('/api/v1/portfolio')
        ->assertUnauthorized()
        ->assertJson([
            'success' => false,
            'message' => 'Invalid API key.',
        ]);
});
