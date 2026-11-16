<?php

use App\Models\Achievement;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSettings;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\User;
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

it('renders published projects with their dialog data', function () {
    SiteSettings::create();
    Project::factory()->create([
        'title' => 'OpenClaw AI Agent',
        'category' => 'Script',
        'description' => "A self-hosted agent.\n\nCore Contributions:\n\nInfrastructure: Runs as a systemd service.",
        'images' => ['projects/openclaw.png'],
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('href="#work"', false)
        ->assertSeeText('OpenClaw AI Agent')
        ->assertSee('src="'.asset('storage/projects/openclaw.png').'"', false)
        ->assertSee('"label":"Infrastructure","text":"Runs as a systemd service."', false)
        ->assertSee('id="project-modal"', false);
});

it('leaves archived projects off the page', function () {
    SiteSettings::create();
    Project::factory()->create(['title' => 'Visible Project']);
    Project::factory()->create(['title' => 'Archived Project'])->delete();

    $this->get('/')
        ->assertOk()
        ->assertSeeText('Visible Project')
        ->assertDontSeeText('Archived Project');
});

it('shows an empty state and no dialog when there are no projects', function () {
    SiteSettings::create();

    $this->get('/')
        ->assertOk()
        ->assertSeeText('No projects have been published yet.')
        ->assertDontSee('id="project-modal"', false);
});

it('escapes project content in the page and in the embedded dialog data', function () {
    SiteSettings::create(['first_name' => '<script>alert("name")</script>']);
    Project::factory()->create([
        'title' => 'Evil </script><script>alert(1)</script>',
        'description' => '<img src=x onerror=alert(2)>',
    ]);

    $response = $this->get('/')->assertOk();

    $response->assertSee('&lt;script&gt;alert(&quot;name&quot;)&lt;/script&gt;', false);
    expect($response->getContent())
        ->not->toContain('<script>alert("name")</script>')
        ->not->toContain('</script><script>alert(1)</script>')
        ->not->toContain('<img src=x onerror=alert(2)>');
});

it('shows project changes made through the admin API immediately', function () {
    SiteSettings::create();
    $project = Project::factory()->create(['title' => 'Old Title']);

    $this->get('/')->assertSeeText('Old Title');

    $this->actingAs(User::factory()->create(), 'sanctum')
        ->putJson("/api/v1/admin/projects/{$project->id}", ['title' => 'New Title'])
        ->assertOk();

    $this->get('/')
        ->assertSeeText('New Title')
        ->assertDontSeeText('Old Title');
});

it('renders expertise, stack, log, about and contact from the CMS', function () {
    SiteSettings::create([
        'first_name' => 'Mohammed',
        'last_name' => 'Zomlot',
        'about_me' => 'I own the whole path of a request.',
        'location' => 'Gaza Strip, Palestine',
        'email' => 'hello@example.com',
        'cv_file' => 'cv/resume.pdf',
        'social_links' => [['name' => 'Telegram', 'url' => 'https://t.me/example']],
    ]);
    Project::factory()->create(['title' => 'Moodle Bot', 'tech_stack' => ['Linux', 'Nginx']]);
    Service::factory()->create(['title' => 'DevOps & Server Management', 'tags' => ['Docker', 'Linux']]);
    Skill::factory()->for(SkillCategory::factory()->create(['name' => 'Programming & Tools']), 'category')->create(['name' => 'Python']);
    Experience::factory()->create(['job_title' => 'Network Engineering Intern', 'company' => 'Shuhada Al-Aqsa Hospital']);
    Education::factory()->create(['degree' => 'Bachelor', 'field_of_study' => 'Software Engineering', 'institution' => 'Al-Azhar University']);
    Achievement::factory()->create(['title' => '1st Place - Hebron Code Jam', 'certificate_url' => null]);

    $this->get('/')
        ->assertOk()
        ->assertSeeInOrder(['href="#work"', 'href="#expertise"', 'href="#stack"', 'href="#log"', 'href="#about"', 'href="#contact"'], false)
        ->assertSeeText('DevOps & Server Management')
        ->assertSeeText('Seen in')
        ->assertSeeText('Programming & Tools')
        ->assertSeeText('Python')
        ->assertSeeText('Network Engineering Intern')
        ->assertSeeText('Bachelor, Software Engineering')
        ->assertSeeInOrder(['1st Place - Hebron Code Jam', 'award'])
        ->assertSeeText('I own the whole path of a request.')
        ->assertSeeText('Gaza Strip, Palestine')
        ->assertSee('mailto:hello@example.com', false)
        ->assertSeeText('For a quick question, Telegram is fine too.')
        ->assertSee('href="https://t.me/example"', false)
        ->assertSeeText('@example')
        ->assertSee('href="'.asset('storage/cv/resume.pdf').'"', false);
});

it('leaves sections without content out of the page and the navigation', function () {
    SiteSettings::create();
    Achievement::factory()->create(['title' => 'Archived Award'])->delete();

    $this->get('/')
        ->assertOk()
        ->assertDontSee('id="expertise"', false)
        ->assertDontSee('href="#expertise"', false)
        ->assertDontSee('id="stack"', false)
        ->assertDontSee('id="log"', false)
        ->assertDontSeeText('Archived Award')
        ->assertSee('id="contact"', false);
});
