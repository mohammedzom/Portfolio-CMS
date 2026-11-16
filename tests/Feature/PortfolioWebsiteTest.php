<?php

use App\Models\SiteSettings;
use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Queue::fake();
});

it('renders the hero from the site settings without calling the REST API', function () {
    Http::preventStrayRequests();
    config(['app.api_key' => 'test-secret-api-key']);
    SiteSettings::create([
        'first_name' => 'Mohammed',
        'last_name' => 'Zomlot',
        'tagline' => 'Software Engineer',
        'bio' => 'Builds backend systems with Laravel.',
        'url_suffix' => 'dev',
        'social_links' => [['name' => 'github', 'url' => 'https://github.com/example']],
    ]);
    $category = SkillCategory::factory()->create(['name' => 'Backend Development']);
    Skill::factory()->for($category, 'category')->create(['name' => 'Laravel', 'proficiency' => 90]);
    Skill::factory()->for($category, 'category')->create(['name' => 'PHP', 'proficiency' => 95]);

    $this->get('/')
        ->assertOk()
        ->assertSeeInOrder(['MZ', '.', 'DEV'])
        ->assertSeeText('Mohammed Zomlot — Software Engineer')
        ->assertSeeText('Builds backend systems with Laravel.')
        ->assertSeeInOrder(['<li>PHP</li>', '<li>Laravel</li>'], false)
        ->assertSee('href="https://github.com/example"', false)
        ->assertSeeText('Trace a request')
        ->assertDontSee('test-secret-api-key');
});

it('renders the hero without optional content before the site settings exist', function () {
    $this->get('/')
        ->assertOk()
        ->assertSeeText('Engineering what happens')
        ->assertDontSee('class="hero__who"', false)
        ->assertDontSee('class="hero__focus"', false)
        ->assertDontSeeText('GitHub profile');
});
