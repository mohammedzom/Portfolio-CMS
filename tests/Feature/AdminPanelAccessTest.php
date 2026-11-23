<?php

use App\Models\User;
use Filament\Auth\Pages\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['app.admin_emails' => ['admin@example.com']]);
});

/**
 * The first Livewire snapshot rendered on a page, as the browser would send it back.
 */
function firstLivewireSnapshot(string $html): string
{
    preg_match('/wire:snapshot="([^"]+)"/', $html, $matches);

    expect($matches)->not->toBeEmpty('The page rendered no Livewire component.');

    return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5);
}

it('redirects guests to the panel sign-in page', function () {
    $this->get('/admin')->assertRedirect('/admin/login');

    $this->get('/admin/login')
        ->assertOk()
        ->assertSeeLivewire(Login::class);
});

it('opens the panel for an allowlisted admin regardless of e-mail case', function () {
    $admin = User::factory()->create(['email' => 'Admin@Example.com']);

    $this->actingAs($admin)->get('/admin')->assertOk();
});

it('returns 403 to a signed-in user who is not allowlisted', function () {
    $user = User::factory()->create(['email' => 'someone@example.com']);

    $this->actingAs($user)->get('/admin')->assertForbidden();
});

it('lets nobody in when no admin e-mail is configured', function () {
    config(['app.admin_emails' => []]);
    $user = User::factory()->create(['email' => 'admin@example.com']);

    $this->actingAs($user)->get('/admin')->assertForbidden();
});

it('signs an allowlisted admin in through the login form', function () {
    $admin = User::factory()->create(['email' => 'admin@example.com']);

    Livewire::test(Login::class)
        ->fillForm(['email' => 'admin@example.com', 'password' => 'password'])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect('/admin');

    $this->assertAuthenticatedAs($admin);
});

it('refuses to sign in a user who is not allowlisted, even with the right password', function () {
    User::factory()->create(['email' => 'someone@example.com']);

    Livewire::test(Login::class)
        ->fillForm(['email' => 'someone@example.com', 'password' => 'password'])
        ->call('authenticate')
        ->assertHasFormErrors(['email']);

    $this->assertGuest();
});

it('returns 403 when a user who is not allowlisted replays a panel Livewire request', function () {
    $admin = User::factory()->create(['email' => 'admin@example.com']);
    $user = User::factory()->create(['email' => 'someone@example.com']);

    $snapshot = firstLivewireSnapshot($this->actingAs($admin)->get('/admin')->assertOk()->getContent());
    $replay = function (User $as) use ($snapshot) {
        // Livewire keeps per-request state in memory; under PHP-FPM every request starts fresh.
        Livewire::flushState();

        return $this->actingAs($as)->postJson(
            route('default-livewire.update'),
            ['components' => [['snapshot' => $snapshot, 'updates' => [], 'calls' => [['method' => '$refresh', 'params' => []]]]]],
            ['X-Livewire' => 'true'],
        );
    };

    $replay($admin)->assertOk();
    $replay($user)->assertForbidden();
});

it('keeps Livewire and Filament assets off the public portfolio page', function () {
    // Earlier tests in this process may have rendered a Livewire component; production requests start fresh.
    Livewire::flushState();

    $this->get('/')
        ->assertOk()
        ->assertDontSee('livewire', false)
        ->assertDontSee('filament', false);
});
