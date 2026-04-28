<?php

use App\Models\SiteSettings;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\getJson;
use function Pest\Laravel\withHeaders;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\DatabaseSeeder::class);
});

it('logs a visit when accessing portfolio', function () {
    $initialCount = Visit::count();

    withHeaders(['x-api-key' => config('app.api_key')])
        ->getJson('/api/v1/portfolio')
        ->assertStatus(200);

    expect(Visit::count())->toBe($initialCount + 1);
});

it('does not log a visit for bots', function () {
    $initialCount = Visit::count();

    withHeaders([
        'x-api-key' => config('app.api_key'),
        'User-Agent' => 'Googlebot/2.1 (+http://www.google.com/bot.html)',
    ])
        ->getJson('/api/v1/portfolio')
        ->assertStatus(200);

    expect(Visit::count())->toBe($initialCount);
});
