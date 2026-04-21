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

class PortfolioController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('sort_order')->get();
        $skills = Skill::orderBy('proficiency', 'desc')->get();
        $services = Service::orderBy('sort_order')->get();
        $experiences = Experience::orderBy('start_date', 'desc')->get();
        $settings = SiteSettings::firstOrFail();

        return response()->json([
            'projects' => ProjectResource::collection($projects),
            'skills' => SkillResource::collection($skills),
            'services' => ServiceResource::collection($services),
            'settings' => new SiteSettingstResource($settings),
            'experiences' => ExperienceResource::collection($experiences),
        ]);
    }
}
