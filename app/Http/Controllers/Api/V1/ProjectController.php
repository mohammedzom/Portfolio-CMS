<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Projects\StoreProjectAction;
use App\Actions\Projects\UpdateProjectAction;
use App\Actions\UpdateSortOrderAction;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Projects\RestoreProjectRequest;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Requests\ReorderRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\Public\ProjectResource as PublicProjectResource;
use App\Models\Project;
use App\Traits\ManagesCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    use ManagesCache;

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
            $projects = Cache::remember($cache_key, $ttl, fn () => $this->resolveForCache(ProjectResource::collection($query->ordered()->get())));
        } else {
            $projects = $this->resolveForCache(ProjectResource::collection($query->ordered()->get()));
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

        $this->forgetProjectCache($project->slug);

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
        $this->forgetProjectCache($oldSlug);
        $this->forgetProjectCache($project->slug);

        return $this->successResponse(
            new ProjectResource($project),
            'Project updated successfully.'
        );
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete();
        $this->forgetProjectCache($project->slug);

        return $this->successResponse(
            [],
            'Project Archived successfully.'
        );
    }

    protected function afterRestore(Project $project): void
    {
        $this->forgetProjectCache($project->slug);
    }

    public function restore(RestoreProjectRequest $request, Project $project): JsonResponse
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
        $this->forgetProjectCache($project->slug);
    }

    public function reorder(ReorderRequest $request): JsonResponse
    {
        UpdateSortOrderAction::run(Project::class, $request->validated()['items']);
        Cache::forget('projects');

        return $this->successResponse(
            [],
            'Projects reordered successfully.'
        );
    }
}
