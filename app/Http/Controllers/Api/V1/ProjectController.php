<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Projects\StoreProjectAction;
use App\Actions\Projects\UpdateProjectAction;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\Public\ProjectResource as PublicProjectResource;
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
        $cache_key = 'projects';

        if ($request->boolean('archived')) {
            $query->onlyTrashed();
            $cache_key .= '_archived';
        }

        if ($request->filled('search')) {
            $cache_key = null;
            $search = $request->string('search')->toString();
            $query->where(function ($query) use ($search): void {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
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

    public function show(Project $project): JsonResponse
    {
        return $this->successResponse(
            new ProjectResource($project),
            'Project fetched successfully.'
        );
    }

    public function publicShow(string $slug): JsonResponse
    {
        $hours = intval(config('app.cache_ttl_hours', 24));
        $ttl = now()->addHours($hours);
        $project = Cache::remember('project_'.$slug, $ttl, function () use ($slug) {
            return $this->resolveForCache(new PublicProjectResource(Project::withoutTrashed()->where('slug', $slug)->firstOrFail()));
        });

        return $this->successResponse(
            $project,
            'Project fetched successfully.'
        );
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = StoreProjectAction::run(
            $request->validated(),
            $request->hasFile('images') ? $request->file('images') : []
        );

        Cache::forget('projects');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new ProjectResource($project),
            'Project created successfully.',
            201
        );
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $oldSlug = $project->slug;

        $project = UpdateProjectAction::run(
            $project,
            $request->validated(),
            $request->deleted_images ?? [],
            $request->hasFile('images') ? $request->file('images') : []
        );
        Cache::forget('project_'.$oldSlug);
        Cache::forget('project_'.$project->slug);
        Cache::forget('projects');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new ProjectResource($project),
            'Project updated successfully.'
        );
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete();
        Cache::forget('project_'.$project->slug);
        Cache::forget('projects');
        Cache::forget('projects_archived');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            [],
            'Project Archived successfully.'
        );
    }

    protected function afterRestore(Project $project): void
    {
        Cache::forget('project_'.$project->slug);
        Cache::forget('projects');
        Cache::forget('projects_archived');
        Cache::forget('portfolio_all');
    }

    public function restore(Project $project): JsonResponse
    {
        $project->restore();
        $this->afterRestore($project);

        return $this->successResponse(
            new ProjectResource($project),
            'Project restored successfully.'
        );
    }

    protected function beforeForceDelete(Project $project): void
    {
        $images = $project->images;
        if (! empty($images) && is_array($images)) {
            Storage::disk('public')->delete($images);
        }
    }

    public function forceDelete(Project $project): JsonResponse
    {
        $this->beforeForceDelete($project);
        $project->forceDelete();
        $this->afterForceDelete($project);

        return $this->successResponse(
            [],
            'Project deleted permanently.'
        );
    }

    protected function afterForceDelete(Project $project): void
    {
        Cache::forget('project_'.$project->slug);
        Cache::forget('projects');
        Cache::forget('projects_archived');
        Cache::forget('portfolio_all');
    }
}
