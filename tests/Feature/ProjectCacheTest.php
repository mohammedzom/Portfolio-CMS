<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

it('invalidates old and new project slug caches after slug update', function () {
    $admin = User::factory()->create();
    $project = Project::factory()->create(['slug' => 'old-project']);

    Cache::put('project_old-project', ['stale' => true], now()->addHour());
    Cache::put('project_new-project', ['stale' => true], now()->addHour());

    $this->actingAs($admin, 'sanctum')
        ->patchJson('/api/v1/admin/projects/'.$project->id, [
            'slug' => 'new-project',
        ])
        ->assertSuccessful();

    expect(Cache::has('project_old-project'))->toBeFalse();
    expect(Cache::has('project_new-project'))->toBeFalse();
});
