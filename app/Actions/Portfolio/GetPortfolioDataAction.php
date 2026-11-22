<?php

namespace App\Actions\Portfolio;

use App\Http\Resources\Public\AchievementResource;
use App\Http\Resources\Public\EducationResource;
use App\Http\Resources\Public\ExperienceResource;
use App\Http\Resources\Public\ProjectResource;
use App\Http\Resources\Public\ServiceResource;
use App\Http\Resources\Public\SkillResource;
use App\Http\Resources\SiteSettingsResource;
use App\Models\Achievement;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSettings;
use App\Models\Skill;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * The public portfolio payload, shared by the REST API and the Blade website.
 *
 * Both consumers read the same `portfolio_all` cache entry, so every write
 * action that calls ManagesCache::forgetPortfolioCache() refreshes both.
 */
class GetPortfolioDataAction
{
    use AsAction;

    public const CACHE_KEY = 'portfolio_all';

    /**
     * @return array{
     *     skills: array<string, list<array<string, mixed>>>,
     *     projects: list<array<string, mixed>>,
     *     services: list<array<string, mixed>>,
     *     information: array<string, mixed>,
     *     experiences: list<array<string, mixed>>,
     *     achievements: list<array<string, mixed>>,
     *     educations: list<array<string, mixed>>,
     * }
     *
     * @throws ModelNotFoundException when the site settings row does not exist yet.
     */
    public function handle(): array
    {
        $ttl = now()->addHours(intval(config('app.cache_ttl_hours', 24)));

        return Cache::remember(self::CACHE_KEY, $ttl, function (): array {
            $skills = Skill::with('category')->orderBy('proficiency', 'desc')->get();

            $groupedSkills = $skills->groupBy('category.name')->map(function ($skillGroup) {
                return SkillResource::collection($skillGroup)->resolve();
            });

            $data = [
                'skills' => $groupedSkills,
                'projects' => ProjectResource::collection(Project::ordered()->get())->resolve(),
                'services' => ServiceResource::collection(Service::ordered()->get())->resolve(),
                'information' => (new SiteSettingsResource(SiteSettings::firstOrFail()))->resolve(),
                'experiences' => ExperienceResource::collection(Experience::orderBy('start_date', 'desc')->get())->resolve(),
                'achievements' => AchievementResource::collection(Achievement::orderBy('date', 'desc')->get())->resolve(),
                'educations' => EducationResource::collection(Education::orderBy('start_year', 'desc')->get())->resolve(),
            ];

            return json_decode(json_encode($data), true);
        });
    }
}
