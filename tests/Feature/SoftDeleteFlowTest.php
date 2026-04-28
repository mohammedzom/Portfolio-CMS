<?php

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('soft deletes restores and force deletes a resource', function () {
    $admin = User::factory()->create();
    $service = Service::factory()->create();

    $this->actingAs($admin, 'sanctum')
        ->deleteJson('/api/v1/admin/services/'.$service->id)
        ->assertSuccessful();

    $this->assertSoftDeleted($service);

    $this->actingAs($admin, 'sanctum')
        ->patchJson('/api/v1/admin/services/'.$service->id.'/restore')
        ->assertSuccessful();

    expect($service->fresh()->trashed())->toBeFalse();

    $this->actingAs($admin, 'sanctum')
        ->deleteJson('/api/v1/admin/services/'.$service->id)
        ->assertSuccessful();

    $this->actingAs($admin, 'sanctum')
        ->deleteJson('/api/v1/admin/services/'.$service->id.'/force-delete')
        ->assertSuccessful();

    $this->assertModelMissing($service);
});
