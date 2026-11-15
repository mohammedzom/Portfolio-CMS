<?php

use App\Models\Achievement;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Message;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

dataset('archivable resources', [
    'achievements' => [Achievement::class, 'achievements'],
    'education' => [Education::class, 'education'],
    'experiences' => [Experience::class, 'experiences'],
    'messages' => [Message::class, 'messages'],
    'projects' => [Project::class, 'projects'],
    'services' => [Service::class, 'services'],
    'skills' => [Skill::class, 'skills'],
    'skill categories' => [SkillCategory::class, 'skill-categories'],
]);

it('archives, restores and permanently deletes a resource', function (string $modelClass, string $resource) {
    $admin = User::factory()->create();
    $record = $modelClass::factory()->create();
    $uri = "/api/v1/admin/{$resource}/{$record->id}";

    $this->actingAs($admin, 'sanctum')->deleteJson($uri)->assertOk();
    $this->assertSoftDeleted($record);

    $this->actingAs($admin, 'sanctum')->patchJson("{$uri}/restore")
        ->assertOk()
        ->assertJsonPath('success', true);
    $this->assertNotSoftDeleted($record);

    $this->actingAs($admin, 'sanctum')->deleteJson($uri)->assertOk();
    $this->actingAs($admin, 'sanctum')->deleteJson("{$uri}/force-delete")->assertOk();
    $this->assertModelMissing($record);
})->with('archivable resources');

it('hides archived resources from the standard routes', function (string $modelClass, string $resource) {
    $admin = User::factory()->create();
    $record = $modelClass::factory()->create();
    $record->delete();

    $this->actingAs($admin, 'sanctum')
        ->deleteJson("/api/v1/admin/{$resource}/{$record->id}")
        ->assertNotFound()
        ->assertJsonPath('error_code', 'NOT_FOUND');

    $this->assertSoftDeleted($record);
})->with('archivable resources');

it('returns not found when restoring or deleting a missing resource', function (string $modelClass, string $resource) {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'sanctum')
        ->patchJson("/api/v1/admin/{$resource}/999999/restore")
        ->assertNotFound()
        ->assertJsonPath('error_code', 'NOT_FOUND');

    $this->actingAs($admin, 'sanctum')
        ->deleteJson("/api/v1/admin/{$resource}/999999/force-delete")
        ->assertNotFound()
        ->assertJsonPath('error_code', 'NOT_FOUND');
})->with('archivable resources');

it('requires authentication for lifecycle operations', function (string $modelClass, string $resource) {
    $record = $modelClass::factory()->create();
    $record->delete();
    $uri = "/api/v1/admin/{$resource}/{$record->id}";

    $this->patchJson("{$uri}/restore")->assertUnauthorized();
    $this->deleteJson("{$uri}/force-delete")->assertUnauthorized();

    $this->assertSoftDeleted($record);
})->with('archivable resources');

it('rejects lifecycle operations without a valid api key', function () {
    $admin = User::factory()->create();
    $project = Project::factory()->create();
    $project->delete();

    $this->withoutApiKey()
        ->actingAs($admin, 'sanctum')
        ->patchJson("/api/v1/admin/projects/{$project->id}/restore")
        ->assertUnauthorized();

    $this->assertSoftDeleted($project);
});

it('treats restoring an active resource as a no-op', function () {
    $admin = User::factory()->create();
    $project = Project::factory()->create();

    $this->actingAs($admin, 'sanctum')
        ->patchJson("/api/v1/admin/projects/{$project->id}/restore")
        ->assertOk()
        ->assertJsonPath('data.id', $project->id);

    $this->assertNotSoftDeleted($project);
});

it('makes a restored project visible on its public page again', function () {
    $admin = User::factory()->create();
    $project = Project::factory()->create();
    $publicUri = "/api/v1/projects/{$project->slug}";

    $this->getJson($publicUri)->assertOk();

    $this->actingAs($admin, 'sanctum')->deleteJson("/api/v1/admin/projects/{$project->id}")->assertOk();
    $this->getJson($publicUri)->assertNotFound();

    $this->actingAs($admin, 'sanctum')->patchJson("/api/v1/admin/projects/{$project->id}/restore")->assertOk();
    $this->getJson($publicUri)->assertOk()->assertJsonPath('data.slug', $project->slug);
});
