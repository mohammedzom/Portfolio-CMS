<?php

namespace App\Actions\Portfolio;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Shapes the public portfolio payload (GetPortfolioDataAction) into the view
 * model used by the Blade website.
 *
 * Everything here is derived from CMS content: no projects, numbers or facts
 * are added. Optional data that is missing simply produces empty sections.
 */
class BuildPortfolioPageAction
{
    use AsAction;

    /** Bento card sizes for the first five projects, matching the approved layout. */
    private const LEAD_CARD_SIZES = ['feature', 'tall', 'wide', 'feature-b', 'std'];

    private const SOCIAL_LABELS = [
        'github' => 'GitHub',
        'linkedin' => 'LinkedIn',
        'telegram' => 'Telegram',
        'twitter' => 'X',
        'x' => 'X',
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'youtube' => 'YouTube',
        'whatsapp' => 'WhatsApp',
    ];

    /**
     * @param  array<string, mixed>  $portfolio  The payload returned by GetPortfolioDataAction.
     * @return array<string, mixed>
     */
    public function handle(array $portfolio): array
    {
        $information = (array) ($portfolio['information'] ?? []);
        $projects = $this->projects((array) ($portfolio['projects'] ?? []));
        $skillGroups = (array) ($portfolio['skills'] ?? []);
        $educations = (array) ($portfolio['educations'] ?? []);

        return [
            'person' => $this->person($information),
            'facts' => $this->facts($information, $educations),
            'focus' => $this->focus($skillGroups),
            'projects' => $projects,
            'categories' => $this->categories($projects),
            'expertise' => $this->expertise((array) ($portfolio['services'] ?? []), $projects),
            'stack' => $this->stack($skillGroups, $projects),
            'timeline' => $this->timeline(
                (array) ($portfolio['experiences'] ?? []),
                $educations,
                (array) ($portfolio['achievements'] ?? []),
            ),
        ];
    }

    /**
     * @param  array<string, mixed>  $information
     * @return array<string, mixed>
     */
    private function person(array $information): array
    {
        $firstName = trim((string) ($information['first_name'] ?? ''));
        $lastName = trim((string) ($information['last_name'] ?? ''));
        $name = trim($firstName.' '.$lastName);
        $initials = Str::upper(Str::substr($firstName, 0, 1).Str::substr($lastName, 0, 1));
        $socialLinks = $this->socialLinks((array) ($information['social_links'] ?? []));

        return [
            'name' => $name,
            'initials' => $initials,
            'role' => $this->text($information['tagline'] ?? null),
            'bio' => $this->text($information['bio'] ?? null),
            'about' => $this->paragraphs($information['about_me'] ?? null),
            'avatar' => $this->text($information['avatar'] ?? null),
            'cv' => $this->text($information['cv_file'] ?? null),
            'email' => $this->text($information['email'] ?? null),
            'location' => $this->text($information['location'] ?? null),
            'wordmark' => [
                'prefix' => $initials !== '' ? $initials : Str::upper(Str::substr((string) ($information['url_prefix'] ?? ''), 0, 2)),
                'suffix' => Str::upper((string) ($information['url_suffix'] ?? '')),
            ],
            'social' => $socialLinks,
            'github' => collect($socialLinks)->firstWhere('key', 'github')['url'] ?? null,
            'telegram' => collect($socialLinks)->firstWhere('key', 'telegram')['url'] ?? null,
        ];
    }

    /**
     * @param  array<int, mixed>  $links
     * @return list<array{key: string, label: string, url: string, display: string}>
     */
    private function socialLinks(array $links): array
    {
        return collect($links)
            ->filter(fn (mixed $link): bool => is_array($link) && filled($link['url'] ?? null) && $this->isWebUrl($link['url']))
            ->map(function (array $link): array {
                $key = Str::lower(trim((string) ($link['name'] ?? '')));
                $url = (string) $link['url'];
                $host = Str::of((string) parse_url($url, PHP_URL_HOST))->lower()->replaceStart('www.', '')->toString();
                $path = trim((string) parse_url($url, PHP_URL_PATH), '/');

                return [
                    'key' => $key,
                    'label' => self::SOCIAL_LABELS[$key] ?? Str::headline($key ?: $host),
                    'url' => $url,
                    'display' => $host === 't.me' && $path !== '' ? '@'.$path : trim($host.'/'.$path, '/'),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $information
     * @param  array<int, mixed>  $educations
     * @return list<array{label: string, value: string}>
     */
    private function facts(array $information, array $educations): array
    {
        $latestEducation = collect($educations)->first();
        $languages = collect((array) ($information['languages'] ?? []))
            ->filter(fn (mixed $language): bool => is_array($language) && filled($language['name'] ?? null))
            ->map(fn (array $language): string => filled($language['level'] ?? null)
                ? "{$language['name']} ({$language['level']})"
                : (string) $language['name'])
            ->implode(' · ');

        return collect([
            ['label' => 'Role', 'value' => $this->text($information['tagline'] ?? null)],
            ['label' => 'Studies', 'value' => $latestEducation
                ? collect([$latestEducation['field_of_study'] ?? null, $latestEducation['institution'] ?? null])->filter()->implode(', ')
                : null],
            ['label' => 'Based in', 'value' => $this->text($information['location'] ?? null)],
            ['label' => 'Languages', 'value' => $languages ?: null],
        ])->filter(fn (array $fact): bool => filled($fact['value']))->values()->all();
    }

    /**
     * The strongest skills, used as the hero's focus line.
     *
     * @param  array<string, mixed>  $skillGroups
     * @return list<string>
     */
    private function focus(array $skillGroups): array
    {
        return collect($skillGroups)
            ->flatten(1)
            ->filter(fn (mixed $skill): bool => is_array($skill) && filled($skill['name'] ?? null))
            ->sortByDesc(fn (array $skill): int => (int) ($skill['proficiency'] ?? 0))
            ->take(5)
            ->pluck('name')
            ->values()
            ->all();
    }

    /**
     * @param  array<int, mixed>  $projects
     * @return list<array<string, mixed>>
     */
    private function projects(array $projects): array
    {
        $projects = array_values(array_filter($projects, 'is_array'));
        $sizes = $this->cardSizes(count($projects));

        return collect($projects)->values()->map(function (array $project, int $index) use ($sizes): array {
            ['intro' => $intro, 'points' => $points] = $this->describe($project['description'] ?? null);
            $tags = $this->tags($project['tech_stack'] ?? []);
            $category = $this->text($project['category'] ?? null) ?? 'Projects';
            $title = (string) ($project['title'] ?? '');

            return [
                'slug' => (string) ($project['slug'] ?? Str::slug($title)),
                'title' => $title,
                'category' => $category,
                'categoryKey' => Str::slug($category),
                'summary' => $this->summary($intro[0] ?? ''),
                'description' => $intro,
                'points' => $points,
                'tags' => $tags,
                'techKeys' => array_values(array_unique(array_map(fn (string $tag): string => $this->techKey($tag), $tags))),
                'images' => array_values(array_filter((array) ($project['images'] ?? []), fn (mixed $url): bool => is_string($url) && $url !== '')),
                'liveUrl' => $this->isWebUrl($project['live_url'] ?? null) ? $project['live_url'] : null,
                'repoUrl' => $this->isWebUrl($project['repo_url'] ?? null) ? $project['repo_url'] : null,
                'size' => $sizes[$index],
                'pattern' => $this->pattern($title, $category, $tags),
            ];
        })->all();
    }

    /**
     * Assign bento sizes so any number of projects fills the 12-column grid without gaps.
     *
     * @return list<string>
     */
    private function cardSizes(int $count): array
    {
        $sizes = match (true) {
            $count === 0 => [],
            $count === 1 => ['strip'],
            $count === 2 => ['feature', 'tall'],
            $count === 3 => ['feature', 'tall', 'strip'],
            $count === 4 => ['feature', 'tall', 'half', 'half'],
            default => array_merge(
                self::LEAD_CARD_SIZES,
                array_map(
                    fn (int $position): string => $position === $count - 1 && ($count - 5) % 2 === 1 ? 'strip' : 'half',
                    range(5, max(5, $count - 1)),
                ),
            ),
        };

        return array_slice($sizes, 0, $count);
    }

    /**
     * Split a CMS description into intro paragraphs and, when the author used a
     * heading such as "Core Contributions:", the list of contributions after it.
     *
     * @return array{intro: list<string>, points: list<array{label: ?string, text: string}>}
     */
    private function describe(mixed $description): array
    {
        $paragraphs = $this->paragraphs($description);
        $headingIndex = collect($paragraphs)->search(
            fn (string $paragraph): bool => Str::endsWith($paragraph, ':') && Str::length($paragraph) <= 48,
        );

        if ($headingIndex === false) {
            return ['intro' => $paragraphs, 'points' => []];
        }

        $points = collect(array_slice($paragraphs, $headingIndex + 1))
            ->map(function (string $paragraph): array {
                if (preg_match('/^([^:\n]{2,40}):\s+(.+)$/su', $paragraph, $matches) === 1) {
                    return ['label' => trim($matches[1]), 'text' => trim($matches[2])];
                }

                return ['label' => null, 'text' => $paragraph];
            })
            ->values()
            ->all();

        return ['intro' => array_slice($paragraphs, 0, $headingIndex), 'points' => $points];
    }

    private function summary(string $paragraph): string
    {
        $firstSentence = preg_split('/(?<=[.!?])\s+(?=[A-Z"“(])/u', trim($paragraph), 2)[0] ?? '';

        return Str::limit($firstSentence, 170);
    }

    /**
     * @return list<string>
     */
    private function tags(mixed $techStack): array
    {
        return collect(is_array($techStack) ? $techStack : [])
            ->filter(fn (mixed $tag): bool => is_string($tag))
            ->map(fn (string $tag): string => trim($tag, " \t\n\r\0\x0B,;"))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Pick the abstract placeholder drawn when a project has no screenshot (or it fails to load).
     *
     * @param  list<string>  $tags
     */
    private function pattern(string $title, string $category, array $tags): string
    {
        $haystack = Str::lower($title.' '.$category.' '.implode(' ', $tags));

        return match (true) {
            Str::contains($haystack, ['agent', 'cloud', 'network', 'infrastructure']) => 'topology',
            Str::contains($haystack, ['telegram', 'bot', 'chat']) => 'chat',
            Str::contains($haystack, ['dashboard', 'backoffice', 'admin']) => 'dashboard',
            Str::contains($haystack, ['resume', 'document', 'cms']) => 'doc',
            Str::contains($haystack, ['api', 'rest']) => 'routes',
            default => 'api',
        };
    }

    /**
     * @param  list<array<string, mixed>>  $projects
     * @return list<array{key: string, label: string, count: int}>
     */
    private function categories(array $projects): array
    {
        return collect($projects)
            ->groupBy('categoryKey')
            ->map(fn ($group, string $key): array => ['key' => $key, 'label' => $group->first()['category'], 'count' => $group->count()])
            ->values()
            ->all();
    }

    /**
     * Services become the expertise tabs; their tags are drawn as the pipeline,
     * and "Seen in" lists the projects whose tech stack shares at least one tag.
     *
     * @param  array<int, mixed>  $services
     * @param  list<array<string, mixed>>  $projects
     * @return list<array<string, mixed>>
     */
    private function expertise(array $services, array $projects): array
    {
        return collect($services)
            ->filter(fn (mixed $service): bool => is_array($service) && filled($service['title'] ?? null))
            ->values()
            ->map(function (array $service, int $index) use ($projects): array {
                $steps = $this->tags($service['tags'] ?? []);
                $stepKeys = array_map(fn (string $step): string => $this->techKey($step), $steps);

                return [
                    'key' => 'service-'.($service['id'] ?? $index),
                    'title' => (string) $service['title'],
                    'summary' => implode(', ', array_slice($steps, 0, 3)),
                    'description' => $this->paragraphs($service['description'] ?? null),
                    'steps' => $steps,
                    'proof' => collect($projects)
                        ->filter(fn (array $project): bool => array_intersect($stepKeys, $project['techKeys']) !== [])
                        ->pluck('slug')
                        ->values()
                        ->all(),
                ];
            })
            ->all();
    }

    /**
     * @param  array<string, mixed>  $skillGroups
     * @param  list<array<string, mixed>>  $projects
     * @return list<array{group: string, items: list<array{name: string, glyph: string, projects: list<string>}>}>
     */
    private function stack(array $skillGroups, array $projects): array
    {
        return collect($skillGroups)
            ->map(function (mixed $skills, string|int $group) use ($projects): array {
                $items = collect(is_array($skills) ? $skills : [])
                    ->filter(fn (mixed $skill): bool => is_array($skill) && filled($skill['name'] ?? null))
                    ->map(function (array $skill) use ($projects): array {
                        $aliases = $this->skillAliases((string) $skill['name']);

                        return [
                            'name' => (string) $skill['name'],
                            'glyph' => $this->glyph((string) $skill['name']),
                            'projects' => collect($projects)
                                ->filter(fn (array $project): bool => array_intersect($aliases, $project['techKeys']) !== [])
                                ->pluck('slug')
                                ->values()
                                ->all(),
                        ];
                    })
                    ->values()
                    ->all();

                return ['group' => is_string($group) && $group !== '' ? $group : 'Other', 'items' => $items];
            })
            ->filter(fn (array $group): bool => $group['items'] !== [])
            ->values()
            ->all();
    }

    /**
     * Combine experience, education and achievements into one reverse-chronological log.
     *
     * Achievements with an uploaded certificate are shown as certificates; the
     * others (contests, awards) get the filled "award" marker.
     *
     * @param  array<int, mixed>  $experiences
     * @param  array<int, mixed>  $educations
     * @param  array<int, mixed>  $achievements
     * @return list<array<string, mixed>>
     */
    private function timeline(array $experiences, array $educations, array $achievements): array
    {
        $entries = collect();

        foreach (array_filter($experiences, 'is_array') as $experience) {
            [$start, $end] = array_pad(array_map('trim', explode('-', (string) ($experience['period'] ?? ''), 2)), 2, '');

            $entries->push([
                'kind' => 'work',
                'when' => $this->range($start, $end),
                'sort' => sprintf('%04d', (int) $start),
                'title' => (string) ($experience['job_title'] ?? ''),
                'org' => $this->text($experience['company'] ?? null),
                'body' => $this->text($experience['description'] ?? null),
                'links' => [],
            ]);
        }

        foreach (array_filter($educations, 'is_array') as $education) {
            $entries->push([
                'kind' => 'education',
                'when' => $this->range((string) ($education['start_year'] ?? ''), (string) ($education['end_year'] ?? '') ?: 'Present'),
                'sort' => sprintf('%04d', (int) ($education['start_year'] ?? 0)),
                'title' => collect([$education['degree'] ?? null, $education['field_of_study'] ?? null])->filter()->implode(', '),
                'org' => $this->text($education['institution'] ?? null),
                'body' => $this->text($education['description'] ?? null),
                'links' => [],
            ]);
        }

        foreach (array_filter($achievements, 'is_array') as $achievement) {
            $date = $this->date($achievement['date'] ?? null);
            $certificate = $this->text($achievement['certificate_url'] ?? null);

            $entries->push([
                'kind' => $certificate ? 'cert' : 'award',
                'when' => $date?->format('M Y') ?? '',
                'sort' => $date?->format('Y-m-d') ?? '0000',
                'title' => (string) ($achievement['title'] ?? ''),
                'org' => $this->text($achievement['issuer'] ?? null),
                'body' => $this->text($achievement['description'] ?? null),
                'links' => array_values(array_filter([
                    $certificate ? ['label' => 'View certificate', 'url' => $certificate] : null,
                    $this->isWebUrl($achievement['url'] ?? null) ? ['label' => 'Details', 'url' => $achievement['url']] : null,
                ])),
            ]);
        }

        return $entries
            ->filter(fn (array $entry): bool => $entry['title'] !== '')
            ->sortByDesc('sort')
            ->values()
            ->map(fn (array $entry): array => Arr::except($entry, 'sort'))
            ->all();
    }

    private function range(string $start, string $end): string
    {
        $start = trim($start);
        $end = trim($end);

        if ($start === '' || $start === $end) {
            return $end ?: $start;
        }

        return $end === '' ? $start : "{$start} – {$end}";
    }

    /**
     * A comparable key for a technology name: "Laravel 13" and "laravel" match,
     * as do "Tailwind CSS v4" and "Tailwind CSS".
     */
    private function techKey(string $name): string
    {
        $name = Str::lower($name);
        $name = preg_replace('/\bv?\d+(?:\.\d+)*\b/u', '', $name) ?? $name;

        return preg_replace('/[^a-z0-9+#]/u', '', $name) ?? '';
    }

    /**
     * "Git & GitHub" and "Linux/Unix" each match any of their parts.
     *
     * @return list<string>
     */
    private function skillAliases(string $name): array
    {
        return collect(preg_split('/\s*[\/&]\s*/u', $name) ?: [])
            ->push($name)
            ->map(fn (string $part): string => $this->techKey($part))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function glyph(string $name): string
    {
        $words = preg_split('/[\s\/&.+-]+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [$name];

        if (count($words) === 1) {
            return Str::ucfirst(Str::lower(Str::substr($words[0], 0, 2)));
        }

        return Str::upper(Str::substr($words[0], 0, 1).Str::substr($words[1], 0, 1));
    }

    /**
     * @return list<string>
     */
    private function paragraphs(mixed $text): array
    {
        if (! is_string($text) || trim($text) === '') {
            return [];
        }

        return collect(preg_split('/\R\s*\R|\R/u', trim($text)) ?: [])
            ->map(fn (string $paragraph): string => trim($paragraph))
            ->filter()
            ->values()
            ->all();
    }

    private function text(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    private function date(mixed $value): ?Carbon
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function isWebUrl(mixed $url): bool
    {
        return is_string($url) && preg_match('#^https?://#i', $url) === 1 && filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}
