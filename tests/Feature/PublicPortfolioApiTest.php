<?php

use App\Models\Project;
use App\Models\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('excludes lifecycle metadata from public portfolio resources', function () {
    Queue::fake();
    SiteSettings::create();
    Project::factory()->create(['slug' => 'public-project']);

    $response = $this->getJson('/api/v1/portfolio')
        ->assertSuccessful();

    $project = $response->json('data.projects.0');

    expect($project)->not->toHaveKey('created_at');
    expect($project)->not->toHaveKey('updated_at');
    expect($project)->not->toHaveKey('deleted_at');
    expect($project)->not->toHaveKey('deleted_at_human');
});

it('excludes lifecycle metadata from public project detail resources', function () {
    $project = Project::factory()->create(['slug' => 'public-project']);

    $response = $this->getJson('/api/v1/projects/'.$project->slug)
        ->assertSuccessful();

    $data = $response->json('data');

    expect($data)->not->toHaveKey('created_at');
    expect($data)->not->toHaveKey('updated_at');
    expect($data)->not->toHaveKey('deleted_at');
    expect($data)->not->toHaveKey('deleted_at_human');
});
