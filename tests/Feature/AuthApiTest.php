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
