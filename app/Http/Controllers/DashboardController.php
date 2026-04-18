<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Project;
use App\Models\SiteSettings;
use App\Models\Skill;

class DashboardController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('sort_order')->take(5)->get();
        $skills = Skill::orderBy('proficiency', 'desc')->take(6)->get();
        $settings = SiteSettings::firstOrFail();
        $messages = Message::latest()->take(3)->get();

        $projectsCount = Project::whereNotNull('deleted_at')->count();
        $messagesCount = Message::count();
        $messagesCountnew = Message::where('is_read', false)->count();

        $skillsCount = Skill::whereNotNull('deleted_at')->count();

        $languages = $settings->languages ?? [];

        $social_links = $settings->social_links ?? [];

        return view('admin.dashboard', compact('projects', 'skills', 'settings', 'social_links', 'languages', 'projectsCount', 'messagesCount', 'messages', 'messagesCountnew', 'skillsCount'));
    }
}
