<?php

use App\Models\Education;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can fetch all education for admin', function () {
    $admin = User::factory()->create();
    Education::factory()->count(3)->create();

    $response = test()->actingAs($admin, 'sanctum')->getJson('/api/v1/admin/education');

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'message', 'data']);
});
