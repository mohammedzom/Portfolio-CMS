<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

it('can fetch all projects for admin', function () {
    $admin = User::factory()->create();
    Project::factory()->count(3)->create();

    $response = test()->actingAs($admin, 'sanctum')->getJson('/api/v1/admin/projects');

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'message', 'data']);
});

it('can create a project via action', function () {
    Storage::fake('public');

    $admin = User::factory()->create();

    $file = UploadedFile::fake()->image('test-project.jpg');

    $response = test()->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/projects', [
        'title' => 'New Test Project',
        'description' => 'A great project',
        'category' => 'Web',
        'tech_stack' => ['Laravel', 'Vue'],
        'images' => [$file],
        'is_featured' => true,
    ]);

    $response->assertStatus(201);

    $project = Project::where('title', 'New Test Project')->first();
    expect($project)->not->toBeNull();
    expect($project->images)->toBeArray();
    expect(count($project->images))->toBe(1);

    Storage::disk('public')->assertExists($project->images[0]);
});

it('can update a project via action', function () {
    Storage::fake('public');

    $admin = User::factory()->create();
    $project = Project::factory()->create(['images' => ['projects/old-image.jpg']]);

    $response = test()->actingAs($admin, 'sanctum')->patchJson('/api/v1/admin/projects/'.$project->id, [
        'title' => 'Updated Project',
    ]);

    $response->assertStatus(200);

    $project->refresh();
    expect($project->title)->toBe('Updated Project');
});

it('can soft delete a project', function () {
    $admin = User::factory()->create();
    $project = Project::factory()->create();

    $response = test()->actingAs($admin, 'sanctum')->deleteJson('/api/v1/admin/projects/'.$project->id);

    $response->assertStatus(200);
    test()->assertSoftDeleted($project);
});
