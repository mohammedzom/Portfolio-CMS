<?php

use App\Models\Achievement;
use App\Models\Project;
use App\Models\SiteSettings;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
    $this->admin = User::factory()->create();
});

function failNextModelWrite(string $modelClass, string $event): void
{
    $modelClass::$event(function (): never {
        throw new RuntimeException('Simulated database failure.');
    });
}

describe('project images', function () {
    it('stores uploads under generated names that ignore unsafe client names', function () {
        $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/projects', [
            'title' => 'Safe Names',
            'description' => 'Project description',
            'category' => 'Web',
            'images' => [UploadedFile::fake()->image('../../My Évil  File!.jpg')],
        ])->assertCreated();

        $path = Project::firstWhere('title', 'Safe Names')->images[0];

        expect($path)->toMatch('/^projects\/my-evil-file-[a-z0-9]{16}\.jpg$/');
        Storage::disk('public')->assertExists($path);
    });

    it('removes uploaded images when the project cannot be saved', function () {
        failNextModelWrite(Project::class, 'creating');

        $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/projects', [
            'title' => 'Doomed',
            'description' => 'Project description',
            'category' => 'Web',
            'images' => [UploadedFile::fake()->image('one.jpg'), UploadedFile::fake()->image('two.jpg')],
        ])->assertServerError();

        expect(Storage::disk('public')->allFiles('projects'))->toBeEmpty();
        $this->assertDatabaseMissing('projects', ['title' => 'Doomed']);
    });

    it('replaces only the requested images after the update commits', function () {
        Storage::disk('public')->put('projects/keep.jpg', 'keep');
        Storage::disk('public')->put('projects/remove.jpg', 'remove');
        $project = Project::factory()->create(['images' => ['projects/keep.jpg', 'projects/remove.jpg']]);

        $this->actingAs($this->admin, 'sanctum')->post("/api/v1/admin/projects/{$project->id}", [
            '_method' => 'PATCH',
            'deleted_images' => [asset('storage/projects/remove.jpg')],
            'images' => [UploadedFile::fake()->image('new.png')],
        ], ['Accept' => 'application/json'])->assertOk();

        $images = $project->fresh()->images;

        expect($images)->toHaveCount(2)
            ->and($images[0])->toBe('projects/keep.jpg');
        Storage::disk('public')->assertExists(['projects/keep.jpg', $images[1]]);
        Storage::disk('public')->assertMissing('projects/remove.jpg');
    });

    it('keeps existing images and discards new uploads when the update fails', function () {
        Storage::disk('public')->put('projects/remove.jpg', 'remove');
        $project = Project::factory()->create(['images' => ['projects/remove.jpg']]);
        failNextModelWrite(Project::class, 'updating');

        $this->actingAs($this->admin, 'sanctum')->post("/api/v1/admin/projects/{$project->id}", [
            '_method' => 'PATCH',
            'deleted_images' => ['projects/remove.jpg'],
            'images' => [UploadedFile::fake()->image('new.png')],
        ], ['Accept' => 'application/json'])->assertServerError();

        expect($project->fresh()->images)->toBe(['projects/remove.jpg'])
            ->and(Storage::disk('public')->allFiles('projects'))->toBe(['projects/remove.jpg']);
    });

    it('never deletes files that do not belong to the project', function () {
        Storage::disk('public')->put('projects/other.jpg', 'other project');
        Storage::disk('public')->put('cv/resume.pdf', 'cv');
        $otherProject = Project::factory()->create(['images' => ['projects/other.jpg']]);
        $project = Project::factory()->create(['images' => []]);

        $this->actingAs($this->admin, 'sanctum')->patchJson("/api/v1/admin/projects/{$project->id}", [
            'deleted_images' => ['projects/other.jpg', '../cv/resume.pdf', '../../.env'],
        ])->assertOk();

        Storage::disk('public')->assertExists(['projects/other.jpg', 'cv/resume.pdf']);
        expect($otherProject->fresh()->images)->toBe(['projects/other.jpg']);
    });

    it('keeps images while archived and deletes them on permanent deletion', function () {
        Storage::disk('public')->put('projects/a.jpg', 'a');
        $project = Project::factory()->create(['images' => ['projects/a.jpg']]);

        $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/projects/{$project->id}")->assertOk();
        Storage::disk('public')->assertExists('projects/a.jpg');

        $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/projects/{$project->id}/force-delete")->assertOk();
        Storage::disk('public')->assertMissing('projects/a.jpg');
        $this->assertModelMissing($project);
    });

    it('keeps images when permanent deletion fails', function () {
        Storage::disk('public')->put('projects/a.jpg', 'a');
        $project = Project::factory()->create(['images' => ['projects/a.jpg']]);
        failNextModelWrite(Project::class, 'forceDeleting');

        $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/projects/{$project->id}/force-delete")->assertServerError();

        Storage::disk('public')->assertExists('projects/a.jpg');
        $this->assertModelExists($project);
    });

    it('rejects uploads that are not images', function () {
        $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/projects', [
            'title' => 'Bad Upload',
            'description' => 'Project description',
            'category' => 'Web',
            'images' => [UploadedFile::fake()->create('shell.php', 1, 'application/x-php')],
        ])->assertUnprocessable()->assertJsonValidationErrors('images.0', 'data');

        expect(Storage::disk('public')->allFiles())->toBeEmpty();
    });
});

describe('achievement certificates', function () {
    it('stores certificates with the same client name without overwriting each other', function () {
        foreach (['First', 'Second'] as $title) {
            $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/achievements', [
                'title' => $title,
                'issuer' => 'Issuer',
                'date' => '2025-01-01',
                'file' => UploadedFile::fake()->create('certificate.pdf', 10, 'application/pdf'),
            ])->assertCreated();
        }

        $paths = Achievement::pluck('certificate_url');

        expect($paths->unique())->toHaveCount(2);
        Storage::disk('public')->assertExists($paths->all());
    });

    it('replaces the certificate only after the update commits', function () {
        Storage::disk('public')->put('achievements/old.pdf', 'old');
        $achievement = Achievement::factory()->create(['certificate_url' => 'achievements/old.pdf']);

        $this->actingAs($this->admin, 'sanctum')->post("/api/v1/admin/achievements/{$achievement->id}", [
            '_method' => 'PATCH',
            'file' => UploadedFile::fake()->create('new.pdf', 10, 'application/pdf'),
        ], ['Accept' => 'application/json'])->assertOk();

        $newPath = $achievement->fresh()->certificate_url;

        expect($newPath)->not->toBe('achievements/old.pdf');
        Storage::disk('public')->assertExists($newPath);
        Storage::disk('public')->assertMissing('achievements/old.pdf');
    });

    it('keeps the previous certificate when the update fails', function () {
        Storage::disk('public')->put('achievements/old.pdf', 'old');
        $achievement = Achievement::factory()->create(['certificate_url' => 'achievements/old.pdf']);
        failNextModelWrite(Achievement::class, 'updating');

        $this->actingAs($this->admin, 'sanctum')->post("/api/v1/admin/achievements/{$achievement->id}", [
            '_method' => 'PATCH',
            'file' => UploadedFile::fake()->create('new.pdf', 10, 'application/pdf'),
        ], ['Accept' => 'application/json'])->assertServerError();

        expect($achievement->fresh()->certificate_url)->toBe('achievements/old.pdf')
            ->and(Storage::disk('public')->allFiles('achievements'))->toBe(['achievements/old.pdf']);
    });

    it('deletes the certificate on permanent deletion', function () {
        Storage::disk('public')->put('achievements/old.pdf', 'old');
        $achievement = Achievement::factory()->create(['certificate_url' => 'achievements/old.pdf']);
        $achievement->delete();

        $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/v1/admin/achievements/{$achievement->id}/force-delete")->assertOk();

        Storage::disk('public')->assertMissing('achievements/old.pdf');
    });
});

describe('site settings files', function () {
    it('replaces the avatar and cv after the update commits', function () {
        Storage::disk('public')->put('avatars/avatar.jpg', 'old avatar');
        Storage::disk('public')->put('cv/My_CV.pdf', 'old cv');
        SiteSettings::create(['avatar' => 'avatars/avatar.jpg', 'cv_file' => 'cv/My_CV.pdf']);

        $this->actingAs($this->admin, 'sanctum')->post('/api/v1/admin/site-info', [
            '_method' => 'PATCH',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
            'cv_file' => UploadedFile::fake()->create('My CV.pdf', 10, 'application/pdf'),
        ], ['Accept' => 'application/json'])->assertOk();

        $settings = SiteSettings::first();

        expect($settings->avatar)->toMatch('/^avatars\/avatar-[a-z0-9]{16}\.jpg$/')
            ->and($settings->cv_file)->toMatch('/^cv\/my-cv-[a-z0-9]{16}\.pdf$/');
        Storage::disk('public')->assertExists([$settings->avatar, $settings->cv_file]);
        Storage::disk('public')->assertMissing(['avatars/avatar.jpg', 'cv/My_CV.pdf']);
    });

    it('removes the cv when requested', function () {
        Storage::disk('public')->put('cv/resume.pdf', 'cv');
        SiteSettings::create(['cv_file' => 'cv/resume.pdf']);

        $this->actingAs($this->admin, 'sanctum')->patchJson('/api/v1/admin/site-info', ['delete_cv' => true])
            ->assertOk()
            ->assertJsonPath('data.cv_file', null);

        expect(SiteSettings::first()->cv_file)->toBeNull();
        Storage::disk('public')->assertMissing('cv/resume.pdf');
    });

    it('keeps the previous avatar when the update fails', function () {
        Storage::disk('public')->put('avatars/avatar.jpg', 'old avatar');
        SiteSettings::create(['avatar' => 'avatars/avatar.jpg']);
        failNextModelWrite(SiteSettings::class, 'saving');

        $this->actingAs($this->admin, 'sanctum')->post('/api/v1/admin/site-info', [
            '_method' => 'PATCH',
            'avatar' => UploadedFile::fake()->image('avatar.png'),
        ], ['Accept' => 'application/json'])->assertServerError();

        expect(SiteSettings::first()->avatar)->toBe('avatars/avatar.jpg')
            ->and(Storage::disk('public')->allFiles('avatars'))->toBe(['avatars/avatar.jpg']);
    });

    it('never deletes a stored path outside the managed directories', function () {
        Storage::disk('public')->put('private/notes.txt', 'unrelated');
        SiteSettings::create(['avatar' => 'private/notes.txt']);

        $this->actingAs($this->admin, 'sanctum')->post('/api/v1/admin/site-info', [
            '_method' => 'PATCH',
            'avatar' => UploadedFile::fake()->image('avatar.png'),
        ], ['Accept' => 'application/json'])->assertOk();

        Storage::disk('public')->assertExists('private/notes.txt');
    });

    it('rejects an avatar that is not an image', function () {
        SiteSettings::create();

        $this->actingAs($this->admin, 'sanctum')->post('/api/v1/admin/site-info', [
            '_method' => 'PATCH',
            'avatar' => UploadedFile::fake()->create('avatar.pdf', 10, 'application/pdf'),
        ], ['Accept' => 'application/json'])->assertUnprocessable();

        expect(Storage::disk('public')->allFiles())->toBeEmpty();
    });
});
