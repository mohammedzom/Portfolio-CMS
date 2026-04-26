<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Projects\StoreProjectAction;
use App\Actions\Projects\UpdateProjectAction;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Project::query();
        $cache_key = 'portfolio_projects';
        if ($request->has('archived') && $request->archived) {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }
        if ($request->filled('search')) {
            $cache_key = null;
            $query->where('title', 'like', '%'.$request->search.'%');
            $query->orWhere('description', 'like', '%'.$request->search.'%');
        }

        $hours = intval(config('app.cache_ttl_hours', 24));
        $ttl = now()->addHours($hours);
        if ($cache_key) {
            $projects = Cache::remember($cache_key, $ttl, fn () => $this->resolveForCache(ProjectResource::collection($query->orderBy('sort_order')->get())));
        } else {
            $projects = $this->resolveForCache(ProjectResource::collection($query->orderBy('sort_order')->get()));
        }

        return $this->successResponse(
            $projects,
            'Projects retreived successfully.'
        );
    }

    public function show(string $id): JsonResponse
    {
        $project = Project::withoutTrashed()->findOrFail($id);

        return $this->successResponse(
            new ProjectResource($project),
            'Project fetched successfully.'
        );
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = StoreProjectAction::run(
            $request->validated(),
            $request->hasFile('images') ? $request->file('images') : []
        );

        Cache::forget('portfolio_projects');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new ProjectResource($project),
            'Project created successfully.',
            201
        );
    }

    public function update(UpdateProjectRequest $request, string $id): JsonResponse
    {
        $project = Project::withoutTrashed()->findOrFail($id);

        $project = UpdateProjectAction::run(
            $project,
            $request->validated(),
            $request->deleted_images ?? [],
            $request->hasFile('images') ? $request->file('images') : []
        );

        Cache::forget('portfolio_projects');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new ProjectResource($project),
            'Project updated successfully.'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $project = Project::withoutTrashed()->findOrFail($id);
        $project->delete();
        Cache::forget('portfolio_projects');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            [],
            'Project Archived successfully.'
        );
    }

    public function restore(string $id): JsonResponse
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        $project->restore();
        Cache::forget('portfolio_projects');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new ProjectResource($project),
            'Project restored successfully.'
        );
    }

    public function forceDelete(string $id): JsonResponse
    {
        $project = Project::withTrashed()->findOrFail($id);
        foreach (is_array($project->images) ? $project->images : [] as $pathToDelete) {
            $relativePath = 'projects/'.basename($pathToDelete);
            Storage::disk('public')->delete($relativePath);
        }
        $project->forceDelete();

        Cache::forget('portfolio_projects');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            [],
            'Project deleted successfully.'
        );
    }
}
