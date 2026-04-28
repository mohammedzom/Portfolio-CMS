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
use App\Models\Visit;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $skills = Skill::with('category')->orderBy('proficiency', 'desc')->get();

        $groupedSkills = $skills->groupBy('category.name')->map(function ($skillGroup) {
            return SkillResource::collection($skillGroup)->resolve();
        });

        $projects = Project::withoutTrashed()->orderBy('sort_order')->take(5)->get();
        $settings = SiteSettings::firstOrFail();
        $messages = Message::withoutTrashed()->orderBy('read_at')->take(3)->get();

        $projectsCount = Project::withoutTrashed()->count();
        $messagesCount = Message::withoutTrashed()->count();
        $messagesCountnew = Message::onlyTrashed()->count();

        $skillsCount = Skill::withoutTrashed()->count();

        return $this->successResponse([
            'analytics_data' => $this->getAnalytics(),
            'projects' => ProjectResource::collection($projects),
            'skills' => $groupedSkills,
            'messages' => MessageResource::collection($messages),
            'information' => SiteSettingstResource::make($settings),
            'projects_count' => $projectsCount,
            'messages_count' => [
                'total' => $messagesCount,
                'archived' => $messagesCountnew,
            ],
            'skills_count' => $skillsCount,
        ], 'Dashboard Data Retrieved Successfully');
    }

    public function getAnalytics()
    {
        $stats = Visit::selectRaw('visited_at, count(*) as count')
            ->where('visited_at', '>=', now()->subDays(30))
            ->groupBy('visited_at')
            ->orderBy('visited_at', 'asc')
            ->get();

        return [
            'total_visits' => Visit::count(),
            'chart_data' => $stats,
        ];
    }
}
