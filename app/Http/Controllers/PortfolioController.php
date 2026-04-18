<?php

namespace App\Http\Controllers;

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

        $languages = $settings->languages ?? [];
        $social_links = $settings->social_links ?? [];

        return view('app', compact('projects', 'skills', 'services', 'settings', 'social_links', 'languages', 'experiences'));
    }
}
