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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PortfolioController extends Controller
{
    public function index(): JsonResponse
    {
        $hours = intval(config('app.cache_ttl_hours', 24));
        $ttl = now()->addHours($hours);

        $data = Cache::remember('portfolio_all', $ttl, function () {
            $projects = Project::orderBy('sort_order')->get();
            $technical_skills = Skill::where('type', 'technical')->orderBy('proficiency', 'desc')->get();
            $tool_skills = Skill::where('type', 'tool')->get();
            $services = Service::orderBy('sort_order')->get();
            $experiences = Experience::orderBy('start_date', 'desc')->get();
            $settings = SiteSettings::firstOrFail();

            return $this->resolveForCache([
                'skills' => [
                    'technical' => SkillResource::collection($technical_skills)->resolve(),
                    'tools' => SkillResource::collection($tool_skills)->resolve(),
                ],
                'projects' => ProjectResource::collection($projects)->resolve(),
                'services' => ServiceResource::collection($services)->resolve(),
                'information' => (new SiteSettingstResource($settings))->resolve(),
                'experiences' => ExperienceResource::collection($experiences)->resolve(),
            ]);
        });

        return $this->successResponse($data, 'Portfolio Data Retreived Successfully.');
    }
}
