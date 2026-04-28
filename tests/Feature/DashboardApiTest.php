<?php

use App\Models\SiteSettings;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns dashboard analytics response shape', function () {
    SiteSettings::create();
    Visit::factory()->create(['visited_at' => now()->toDateString()]);
    $admin = User::factory()->create();

    $this->actingAs($admin, 'sanctum')
        ->getJson('/api/v1/admin/dashboard')
        ->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'analytics_data' => [
                    'total_visits',
                    'chart_data',
                ],
                'projects',
                'skills',
                'messages',
                'information',
                'projects_count',
                'messages_count' => ['total', 'archived'],
                'skills_count',
            ],
        ]);
});
