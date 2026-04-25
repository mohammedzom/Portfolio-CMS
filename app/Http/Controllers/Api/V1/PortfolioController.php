<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\AchievementResource;
use App\Http\Resources\EducationResource;
use App\Http\Resources\ExperienceResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\SiteSettingstResource;
use App\Http\Resources\SkillResource;
use App\Models\Achievement;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSettings;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PortfolioController extends Controller
{
    public function index(): JsonResponse
    {
        $hours = intval(config('app.cache_ttl_hours', 24));
        $ttl = now()->addHours($hours);

        $data = Cache::remember('portfolio_all', $ttl, function () {
            $skills = Skill::with('category')->orderBy('proficiency', 'desc')->get();

            $groupedSkills = $skills->groupBy('category.name')->map(function ($skillGroup) {
                return SkillResource::collection($skillGroup)->resolve();
            });

            $projects = Project::orderBy('sort_order')->get();
            $services = Service::orderBy('sort_order')->get();
            $experiences = Experience::orderBy('start_date', 'desc')->get();
            $settings = SiteSettings::firstOrFail();
            $achievements = Achievement::orderBy('date', 'desc')->get();
            $educations = Education::orderBy('start_year', 'desc')->get();

            return $this->resolveForCache([
                'skills' => $groupedSkills,
                'projects' => ProjectResource::collection($projects)->resolve(),
                'services' => ServiceResource::collection($services)->resolve(),
                'information' => (new SiteSettingstResource($settings))->resolve(),
                'experiences' => ExperienceResource::collection($experiences)->resolve(),
                'achievements' => AchievementResource::collection($achievements)->resolve(),
                'educations' => EducationResource::collection($educations)->resolve(),
            ]);
        });

        return $this->successResponse($data, 'Portfolio Data Retrieved Successfully.');
    }
}
