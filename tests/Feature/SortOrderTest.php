<?php

use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('projects are ordered by sort_order', function () {
    Project::factory()->create(['sort_order' => 3]);
    Project::factory()->create(['sort_order' => 1]);
    Project::factory()->create(['sort_order' => 2]);

    $projects = Project::ordered()->get();

    expect($projects[0]->sort_order)->toBe(1);
    expect($projects[1]->sort_order)->toBe(2);
    expect($projects[2]->sort_order)->toBe(3);
});

test('services are ordered by sort_order', function () {
    Service::factory()->create(['sort_order' => 3]);
    Service::factory()->create(['sort_order' => 1]);
    Service::factory()->create(['sort_order' => 2]);

    $services = Service::ordered()->get();

    expect($services[0]->sort_order)->toBe(1);
    expect($services[1]->sort_order)->toBe(2);
    expect($services[2]->sort_order)->toBe(3);
});

test('project auto-assigns sort_order on creation', function () {
    Project::factory()->create(['sort_order' => 1]);
    Project::factory()->create(['sort_order' => 2]);

    $newProject = Project::factory()->create(['sort_order' => null]);

    expect($newProject->sort_order)->toBe(3);
});

test('service auto-assigns sort_order on creation', function () {
    Service::factory()->create(['sort_order' => 1]);
    Service::factory()->create(['sort_order' => 2]);

    $newService = Service::factory()->create(['sort_order' => null]);

    expect($newService->sort_order)->toBe(3);
});

test('projects can be reordered via api', function () {
    $project1 = Project::factory()->create(['sort_order' => 1]);
    $project2 = Project::factory()->create(['sort_order' => 2]);
    $project3 = Project::factory()->create(['sort_order' => 3]);

    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withToken($token)->postJson('/api/v1/admin/projects/reorder', [
        'items' => [
            ['id' => $project3->id, 'sort_order' => 1],
            ['id' => $project1->id, 'sort_order' => 2],
            ['id' => $project2->id, 'sort_order' => 3],
        ],
    ]);

    $response->assertStatus(200);

    $projects = Project::ordered()->get();
    expect($projects[0]->id)->toBe($project3->id);
    expect($projects[1]->id)->toBe($project1->id);
    expect($projects[2]->id)->toBe($project2->id);
});

test('services can be reordered via api', function () {
    $service1 = Service::factory()->create(['sort_order' => 1]);
    $service2 = Service::factory()->create(['sort_order' => 2]);
    $service3 = Service::factory()->create(['sort_order' => 3]);

    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withToken($token)->postJson('/api/v1/admin/services/reorder', [
        'items' => [
            ['id' => $service3->id, 'sort_order' => 1],
            ['id' => $service1->id, 'sort_order' => 2],
            ['id' => $service2->id, 'sort_order' => 3],
        ],
    ]);

    $response->assertStatus(200);

    $services = Service::ordered()->get();
    expect($services[0]->id)->toBe($service3->id);
    expect($services[1]->id)->toBe($service1->id);
    expect($services[2]->id)->toBe($service2->id);
});
