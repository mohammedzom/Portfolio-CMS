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
        $technical_skills = Skill::where('type', 'technical')->orderBy('proficiency', 'desc')->take(6)->get();
        $tool_skills = Skill::where('type', 'tool')->take(6)->get();
        $settings = SiteSettings::firstOrFail();
        $messages = Message::orderBy('read_at')->take(3)->get();

        $projectsCount = Project::withoutTrashed()->count();
        $messagesCount = Message::count();
        $messagesCountnew = Message::whereNull('read_at')->count();

        $skillsCount = Skill::withoutTrashed()->count();

        return $this->successResponse([
            'projects' => ProjectResource::collection($projects),
            'skills' => [
                'technical' => SkillResource::collection($technical_skills),
                'tool' => SkillResource::collection($tool_skills),
            ],
            'messages' => MessageResource::collection($messages),
            'information' => SiteSettingstResource::make($settings),
            'projects_count' => $projectsCount,
            'messages_count' => [
                'total' => $messagesCount,
                'unread' => $messagesCountnew,
            ],
            'skills_count' => $skillsCount,
        ], 'Dashboard Data Retrieved Successfully');
    }
}
