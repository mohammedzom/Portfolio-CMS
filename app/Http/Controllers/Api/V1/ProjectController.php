<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Projects\StoreProjectAction;
use App\Actions\Projects\UpdateProjectAction;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Traits\ManageSoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    use ManageSoftDeletes;

    protected $modelClass = Project::class;

    public function index(Request $request): JsonResponse
    {
        $query = Project::query();
        $cache_key = 'portfolio_projects';
        if ($request->has('archived') && $request->input('archived') == true) {
            $query->onlyTrashed();
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

    public function show(string $slug): JsonResponse
    {
        $hours = intval(config('app.cache_ttl_hours', 24));
        $ttl = now()->addHours($hours);
        $project = Cache::remember('project_'.$slug, $ttl, function () use ($slug) {
            return $this->resolveForCache(new ProjectResource(Project::withoutTrashed()->where('slug', $slug)->firstOrFail()));
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
        Cache::forget('project_'.$project->slug);
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
        Cache::forget('project_'.$project->slug);
        Cache::forget('portfolio_projects');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            [],
            'Project Archived successfully.'
        );
    }

    protected function afterRestore(Project $project): void
    {
        Cache::forget('project_'.$project->slug);
        Cache::forget('portfolio_projects');
        Cache::forget('portfolio_all');
    }

    protected function beforeForceDelete(Project $project): void
    {
        $images = $project->images;
        if (! empty($images) && is_array($images)) {
            Storage::disk('public')->delete($images);
        }
    }

    protected function afterForceDelete(Project $project): void
    {
        Cache::forget('project_'.$project->slug);
        Cache::forget('portfolio_projects');
        Cache::forget('portfolio_all');
    }
}
