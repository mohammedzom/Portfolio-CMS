<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Resources\MessageResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\SiteSettingstResource;
use App\Http\Resources\SkillResource;
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
        $messages = Message::orderBy('read_at')->take(3)->get();

        $projectsCount = Project::withoutTrashed()->count();
        $messagesCount = Message::count();
        $messagesCountnew = Message::whereNull('read_at')->count();

        $skillsCount = Skill::withoutTrashed()->count();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data fetched successfully',
            'data' => [
                'projects' => ProjectResource::collection($projects),
                'skills' => SkillResource::collection($skills),
                'messages' => MessageResource::collection($messages),
                'settings' => SiteSettingstResource::make($settings),
                'projects_count' => $projectsCount,
                'messages_count' => [
                    'total' => $messagesCount,
                    'unread' => $messagesCountnew,
                ],
                'skills_count' => $skillsCount,
            ],
        ]);
    }
}
