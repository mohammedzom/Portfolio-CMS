<?php

use Illuminate\Support\Facades\Route;

it('keeps the status code of http errors instead of returning a server error', function (int $status, string $errorCode) {
    Route::get('/api/v1/__test/http-error', fn () => abort($status, 'internal detail that must not leak'));

    $this->getJson('/api/v1/__test/http-error')
        ->assertStatus($status)
        ->assertJsonPath('success', false)
        ->assertJsonPath('error_code', $errorCode)
        ->assertJsonMissing(['message' => 'internal detail that must not leak']);
})->with([
    'payload too large' => [413, 'PAYLOAD_TOO_LARGE'],
    'service unavailable (maintenance mode)' => [503, 'SERVICE_UNAVAILABLE'],
    'other http errors' => [418, 'HTTP_ERROR'],
]);

it('reports unsupported methods as 405 with the allowed methods', function () {
    $this->putJson('/api/v1/portfolio')
        ->assertStatus(405)
        ->assertJsonPath('error_code', 'METHOD_NOT_ALLOWED')
        ->assertHeader('Allow');
});

it('passes retry headers through for unavailable responses', function () {
    Route::get('/api/v1/__test/unavailable', fn () => abort(503, '', ['Retry-After' => '60']));

    $this->getJson('/api/v1/__test/unavailable')
        ->assertStatus(503)
        ->assertHeader('Retry-After', '60');
});

it('limits the length of contact messages', function () {
    $this->postJson('/api/v1/message', [
        'name' => 'Visitor',
        'email' => 'visitor@example.com',
        'subject' => 'Hello',
        'body' => str_repeat('a', 10001),
    ])->assertUnprocessable()->assertJsonValidationErrors('body', 'data');
});
