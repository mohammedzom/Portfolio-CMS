<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\ExperienceResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\SiteSettingstResource;
use App\Http\Resources\SkillResource;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSettings;
use App\Models\Skill;
use Illuminate\Support\Facades\Cache;

class PortfolioController extends Controller
{
    public function index()
    {
        $hours = config('app.cache_ttl_hours', 24);
        $ttl = now()->addHours($hours);

        $projects = Cache::remember('portfolio_projects', $ttl, fn () => Project::orderBy('sort_order')->get());
        $technical_skills = Cache::remember('portfolio_tech_skills', $ttl, fn () => Skill::where('type', 'technical')->orderBy('proficiency', 'desc')->get());
        $tool_skills = Cache::remember('portfolio_tool_skills', $ttl, fn () => Skill::where('type', 'tool')->get());
        $services = Cache::remember('portfolio_services', $ttl, fn () => Service::orderBy('sort_order')->get());
        $experiences = Cache::remember('portfolio_experiences', $ttl, fn () => Experience::orderBy('start_date', 'desc')->get());
        $settings = Cache::remember('portfolio_settings', $ttl, fn () => SiteSettings::firstOrFail());

        return $this->successResponse([
            'skills' => [
                'technical' => SkillResource::collection($technical_skills),
                'tools' => SkillResource::collection($tool_skills),
            ],
            'projects' => ProjectResource::collection($projects),
            'services' => ServiceResource::collection($services),
            'information' => new SiteSettingstResource($settings),
            'experiences' => ExperienceResource::collection($experiences),
        ], 'Portfolio Data Retreived Successfully.');
    }
}
