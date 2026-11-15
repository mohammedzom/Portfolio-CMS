<?php

use App\Models\SiteSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs in with valid credentials', function () {
    $user = User::factory()->create(['email' => 'admin@example.com']);

    $this->postJson('/api/v1/admin/login', [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'data' => ['token'],
        ]);
});

it('rejects wrong credentials', function () {
    $user = User::factory()->create(['email' => 'admin@example.com']);

    $this->postJson('/api/v1/admin/login', [
        'email' => $user->email,
        'password' => 'not-the-password',
    ])
        ->assertUnauthorized()
        ->assertJsonPath('success', false);
});

it('rejects protected routes without a sanctum token', function () {
    $this->getJson('/api/v1/admin/dashboard')
        ->assertUnauthorized();
});

it('rejects expired sanctum tokens', function () {
    SiteSettings::create();
    $user = User::factory()->create();
    $token = $user->createToken('expired-token', ['*'], now()->subMinute());

    $this->withToken($token->plainTextToken)
        ->getJson('/api/v1/admin/dashboard')
        ->assertUnauthorized();
});

it('does not reveal whether an email address is registered', function () {
    User::factory()->create(['email' => 'admin@example.com']);

    $unknownEmail = $this->postJson('/api/v1/admin/login', [
        'email' => 'nobody@example.com',
        'password' => 'not-the-password',
    ]);

    $wrongPassword = $this->postJson('/api/v1/admin/login', [
        'email' => 'admin@example.com',
        'password' => 'not-the-password',
    ]);

    $unknownEmail->assertUnauthorized();
    expect($unknownEmail->json())->toBe($wrongPassword->json());
});

it('still validates the login payload', function () {
    $this->postJson('/api/v1/admin/login', ['email' => 'not-an-email'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password'], 'data');
});

it('expires login tokens after seven days by default', function () {
    SiteSettings::create();
    $user = User::factory()->create();

    $token = $this->postJson('/api/v1/admin/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->json('data.token');

    $this->travel(6)->days();
    $this->withToken($token)->getJson('/api/v1/admin/dashboard')->assertOk();

    $this->travel(1)->days();
    $this->travel(1)->minutes();
    app('auth')->forgetGuards();
    $this->withToken($token)->getJson('/api/v1/admin/dashboard')->assertUnauthorized();
});
