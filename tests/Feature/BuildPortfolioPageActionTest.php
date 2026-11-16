<?php

use App\Actions\Portfolio\BuildPortfolioPageAction;

/**
 * @param  array<string, mixed>  $attributes
 * @return array<string, mixed>
 */
function portfolioProject(array $attributes = []): array
{
    return array_merge([
        'title' => 'Project',
        'slug' => 'project-'.uniqid(),
        'description' => 'A project.',
        'category' => 'Web',
        'tech_stack' => [],
        'images' => [],
        'live_url' => null,
        'repo_url' => null,
    ], $attributes);
}

it('fills the bento grid without gaps for any number of projects', function (int $count, array $sizes) {
    $page = BuildPortfolioPageAction::run([
        'projects' => array_map(fn (): array => portfolioProject(), range(1, $count)),
    ]);

    expect(array_column($page['projects'], 'size'))->toBe($sizes);
})->with([
    'one project' => [1, ['strip']],
    'two projects' => [2, ['feature', 'tall']],
    'three projects' => [3, ['feature', 'tall', 'strip']],
    'four projects' => [4, ['feature', 'tall', 'half', 'half']],
    'five projects' => [5, ['feature', 'tall', 'wide', 'feature-b', 'std']],
    'six projects (approved layout)' => [6, ['feature', 'tall', 'wide', 'feature-b', 'std', 'strip']],
    'seven projects' => [7, ['feature', 'tall', 'wide', 'feature-b', 'std', 'half', 'half']],
]);

it('turns a contributions heading in the description into labelled points', function () {
    $page = BuildPortfolioPageAction::run(['projects' => [portfolioProject([
        'description' => "A self-hosted agent on Oracle Cloud. It answers in Arabic.\r\n\r\nCore Contributions:\r\n\r\nInfrastructure: Deployed as a systemd service.\r\n\r\nWorked closely with the team.",
    ])]]);

    $project = $page['projects'][0];

    expect($project['summary'])->toBe('A self-hosted agent on Oracle Cloud.')
        ->and($project['description'])->toBe(['A self-hosted agent on Oracle Cloud. It answers in Arabic.'])
        ->and($project['points'])->toBe([
            ['label' => 'Infrastructure', 'text' => 'Deployed as a systemd service.'],
            ['label' => null, 'text' => 'Worked closely with the team.'],
        ]);
});

it('keeps every paragraph as description when there is no contributions heading', function () {
    $page = BuildPortfolioPageAction::run(['projects' => [portfolioProject([
        'description' => "First paragraph.\n\nSecond paragraph.",
    ])]]);

    expect($page['projects'][0]['description'])->toBe(['First paragraph.', 'Second paragraph.'])
        ->and($page['projects'][0]['points'])->toBe([]);
});

it('drops links that are not http or https', function () {
    $page = BuildPortfolioPageAction::run([
        'projects' => [portfolioProject(['live_url' => 'javascript:alert(1)', 'repo_url' => 'https://github.com/example/repo'])],
        'information' => ['social_links' => [
            ['name' => 'github', 'url' => 'https://github.com/example'],
            ['name' => 'evil', 'url' => 'javascript:alert(1)'],
        ]],
    ]);

    expect($page['projects'][0]['liveUrl'])->toBeNull()
        ->and($page['projects'][0]['repoUrl'])->toBe('https://github.com/example/repo')
        ->and(array_column($page['person']['social'], 'url'))->toBe(['https://github.com/example']);
});

it('matches skills to projects across versions and combined skill names', function () {
    $page = BuildPortfolioPageAction::run([
        'projects' => [
            portfolioProject(['slug' => 'bot', 'tech_stack' => ['Laravel 13', 'Linux', 'Tailwind CSS v4']]),
            portfolioProject(['slug' => 'api', 'tech_stack' => ['PHP 8.5', 'GitHub']]),
        ],
        'skills' => ['Backend' => [
            ['name' => 'Laravel', 'proficiency' => 90],
            ['name' => 'Linux/Unix', 'proficiency' => 95],
            ['name' => 'Git & GitHub', 'proficiency' => 90],
            ['name' => 'Flutter', 'proficiency' => 70],
        ]],
    ]);

    $usage = collect($page['stack'][0]['items'])->pluck('projects', 'name')->all();

    expect($usage)->toBe([
        'Laravel' => ['bot'],
        'Linux/Unix' => ['bot'],
        'Git & GitHub' => ['api'],
        'Flutter' => [],
    ]);
});

it('links each service to the projects that share its tags', function () {
    $page = BuildPortfolioPageAction::run([
        'projects' => [
            portfolioProject(['slug' => 'agent', 'tech_stack' => ['Linux', 'systemd']]),
            portfolioProject(['slug' => 'cms', 'tech_stack' => ['Laravel']]),
        ],
        'services' => [['id' => 8, 'title' => 'DevOps & Server Management', 'description' => 'Servers.', 'tags' => ['Docker', 'Linux', 'Nginx']]],
    ]);

    expect($page['expertise'][0])
        ->toMatchArray([
            'key' => 'service-8',
            'steps' => ['Docker', 'Linux', 'Nginx'],
            'proof' => ['agent'],
        ]);
});

it('orders the log newest first and marks achievements without a certificate as awards', function () {
    $page = BuildPortfolioPageAction::run([
        'educations' => [['degree' => 'Bachelor', 'field_of_study' => 'Software Engineering', 'institution' => 'Al-Azhar University', 'start_year' => 2021, 'end_year' => null]],
        'experiences' => [['job_title' => 'Intern', 'company' => 'Hospital', 'period' => '2026 - 2027']],
        'achievements' => [
            ['title' => '1st Place', 'issuer' => 'Contest', 'date' => '2025-04-25', 'certificate_url' => null, 'url' => 'https://example.com/results'],
            ['title' => 'Docker Foundations', 'issuer' => 'LinkedIn Learning', 'date' => '2026-06-13', 'certificate_url' => 'https://example.com/storage/achievements/docker.pdf', 'url' => null],
        ],
    ]);

    expect($page['timeline'])->sequence(
        fn ($entry) => $entry->toMatchArray(['kind' => 'cert', 'title' => 'Docker Foundations', 'when' => 'Jun 2026', 'links' => [['label' => 'View certificate', 'url' => 'https://example.com/storage/achievements/docker.pdf']]]),
        fn ($entry) => $entry->toMatchArray(['kind' => 'work', 'title' => 'Intern', 'when' => '2026 – 2027']),
        fn ($entry) => $entry->toMatchArray(['kind' => 'award', 'title' => '1st Place', 'when' => 'Apr 2025', 'links' => [['label' => 'Details', 'url' => 'https://example.com/results']]]),
        fn ($entry) => $entry->toMatchArray(['kind' => 'education', 'title' => 'Bachelor, Software Engineering', 'when' => '2021 – Present']),
    );
});
