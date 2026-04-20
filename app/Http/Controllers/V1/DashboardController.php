<?php

namespace App\Http\Controllers\V1;

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

        $languages = ! empty($settings->languages)
            ? implode(', ', $settings->languages)
            : 'N/A';

        $social_links = $settings->social_links ?? [];

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data fetched successfully',
            'data' => [
                'projects' => $projects,
                'skills' => $skills,
                'settings' => $settings,
                'social_links' => $social_links,
                'languages' => $languages,
                'projectsCount' => $projectsCount,
                'messagesCount' => $messagesCount,
                'messages' => $messages,
                'messagesCountnew' => $messagesCountnew,
                'skillsCount' => $skillsCount,
            ],
        ]);
    }
}
