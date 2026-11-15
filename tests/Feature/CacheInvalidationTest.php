<?php

use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Testing\TestResponse;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    SiteSettings::create(['first_name' => 'Original']);
    $this->admin = User::factory()->create();
});

function publicPortfolio(): TestResponse
{
    return test()->getJson('/api/v1/portfolio')->assertOk();
}

/**
 * @return list<string>
 */
function publicProjectTitles(): array
{
    return array_column(publicPortfolio()->json('data.projects'), 'title');
}

describe('public portfolio after project changes', function () {
    it('shows a newly created project', function () {
        publicProjectTitles();

        $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/projects', [
            'title' => 'Brand New',
            'description' => 'Description',
            'category' => 'Web',
        ])->assertCreated();

        expect(publicProjectTitles())->toContain('Brand New');
    });

    it('shows an updated project', function () {
        $project = Project::factory()->create(['title' => 'Before']);
        publicProjectTitles();

        $this->actingAs($this->admin, 'sanctum')->patchJson("/api/v1/admin/projects/{$project->id}", ['title' => 'After'])->assertOk();

        expect(publicProjectTitles())->toContain('After')->not->toContain('Before');
    });

    it('hides an archived project and shows it again after restore', function () {
        $project = Project::factory()->create(['title' => 'Lifecycle']);
        publicProjectTitles();

        $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/projects/{$project->id}")->assertOk();
        expect(publicProjectTitles())->not->toContain('Lifecycle');

        $this->actingAs($this->admin, 'sanctum')->patchJson("/api/v1/admin/projects/{$project->id}/restore")->assertOk();
        expect(publicProjectTitles())->toContain('Lifecycle');
    });

    it('removes a permanently deleted project', function () {
        $project = Project::factory()->create(['title' => 'Gone']);
        publicProjectTitles();

        $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/projects/{$project->id}/force-delete")->assertOk();

        expect(publicProjectTitles())->not->toContain('Gone');
    });

    it('reflects a new project order', function () {
        $first = Project::factory()->create(['title' => 'First', 'sort_order' => 1]);
        $second = Project::factory()->create(['title' => 'Second', 'sort_order' => 2]);
        expect(publicProjectTitles())->toBe(['First', 'Second']);

        $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/projects/reorder', [
            'items' => [
                ['id' => $first->id, 'sort_order' => 2],
                ['id' => $second->id, 'sort_order' => 1],
            ],
        ])->assertOk();

        expect(publicProjectTitles())->toBe(['Second', 'First']);
    });
});

it('refreshes a cached public project page after reordering', function () {
    $project = Project::factory()->create(['sort_order' => 1]);
    $this->getJson("/api/v1/projects/{$project->slug}")->assertJsonPath('data.sort_order', 1);

    $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/projects/reorder', [
        'items' => [['id' => $project->id, 'sort_order' => 7]],
    ])->assertOk();

    $this->getJson("/api/v1/projects/{$project->slug}")->assertJsonPath('data.sort_order', 7);
});

it('reflects a new service order in the public portfolio', function () {
    $first = Service::factory()->create(['title' => 'First Service', 'sort_order' => 1]);
    $second = Service::factory()->create(['title' => 'Second Service', 'sort_order' => 2]);
    expect(array_column(publicPortfolio()->json('data.services'), 'title'))->toBe(['First Service', 'Second Service']);

    $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/services/reorder', [
        'items' => [
            ['id' => $first->id, 'sort_order' => 2],
            ['id' => $second->id, 'sort_order' => 1],
        ],
    ])->assertOk();

    expect(array_column(publicPortfolio()->json('data.services'), 'title'))->toBe(['Second Service', 'First Service']);
});

it('keeps the cached admin active and archived lists consistent', function (string $modelClass, string $resource) {
    $record = $modelClass::factory()->create();
    $activeIds = fn (): array => array_column($this->actingAs($this->admin, 'sanctum')->getJson("/api/v1/admin/{$resource}")->json('data'), 'id');
    $archivedIds = fn (): array => array_column($this->actingAs($this->admin, 'sanctum')->getJson("/api/v1/admin/{$resource}?archived=1")->json('data'), 'id');

    expect($activeIds())->toContain($record->id)
        ->and($archivedIds())->not->toContain($record->id);

    $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/{$resource}/{$record->id}")->assertOk();
    expect($activeIds())->not->toContain($record->id)
        ->and($archivedIds())->toContain($record->id);

    $this->actingAs($this->admin, 'sanctum')->patchJson("/api/v1/admin/{$resource}/{$record->id}/restore")->assertOk();
    expect($activeIds())->toContain($record->id)
        ->and($archivedIds())->not->toContain($record->id);
})->with([
    'projects' => [Project::class, 'projects'],
    'services' => [Service::class, 'services'],
    'experiences' => [Experience::class, 'experiences'],
]);

it('refreshes cached site settings after an update', function () {
    $this->actingAs($this->admin, 'sanctum')->getJson('/api/v1/admin/site-info')->assertJsonPath('data.first_name', 'Original');
    publicPortfolio()->assertJsonPath('data.information.first_name', 'Original');

    $this->actingAs($this->admin, 'sanctum')->patchJson('/api/v1/admin/site-info', ['first_name' => 'Updated'])->assertOk();

    $this->actingAs($this->admin, 'sanctum')->getJson('/api/v1/admin/site-info')->assertJsonPath('data.first_name', 'Updated');
    publicPortfolio()->assertJsonPath('data.information.first_name', 'Updated');
});
