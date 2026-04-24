<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();
        $cache_key = 'portfolio_projects';
        if ($request->has('archived') && $request->archived) {
            $query->onlyTrashed();
            $cache_key .= '_archived';
        } else {
            $query->withoutTrashed();
        }
        if ($request->filled('search')) {
            $cache_key = null;
            $query->where('title', 'like', '%'.$request->search.'%');
            $query->orWhere('description', 'like', '%'.$request->search.'%');
        }

        $hours = config('app.cache_ttl_hours', 24);
        $ttl = now()->addHours($hours);
        if ($cache_key) {
            $projects = Cache::remember($cache_key, $ttl, fn () => $query->orderBy('sort_order')->get());
        } else {
            $projects = $query->orderBy('sort_order')->get();
        }

        return $this->successResponse(
            ProjectResource::collection($projects),
            'Projects retreived successfully.'
        );
    }

    public function show(string $id)
    {
        $project = Project::withoutTrashed()->findOrFail($id);

        return $this->successResponse(
            new ProjectResource($project),
            'Project fetched successfully.'
        );
    }

    public function store(StoreProjectRequest $request)
    {
        $paths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $fileOriginalName = explode('.', $file->getClientOriginalName())[0];
                $extension = $file->getClientOriginalExtension();
                $fileName = 'project_'.$fileOriginalName.'_'.uniqid().'.'.$extension;
                $paths[] = $file->storeAs('projects', $fileName, 'public');
            }
        }

        $slug = $request->slug ?? str()->slug($request->title);
        if (Project::where('slug', $slug)->exists()) {
            $slug .= '-'.uniqid();
        }
        $maxSortOrder = Project::withoutTrashed()->max('sort_order') + 1;
        $project = Project::create([
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'category' => $request->category,
            'tech_stack' => $request->tech_stack,
            'live_url' => $request->live_url,
            'repo_url' => $request->repo_url,
            'is_featured' => $request->is_featured ?? false,
            'sort_order' => $request->sort_order ?? $maxSortOrder,
            'images' => $paths,
        ]);
        Cache::forget('portfolio_projects');

        return $this->successResponse(
            new ProjectResource($project),
            'Project created successfully.',
            201
        );
    }

    public function update(UpdateProjectRequest $request, string $id)
    {
        $project = Project::withoutTrashed()->findOrFail($id);

        $currentImages = is_array($project->images) ? $project->images : [];

        if ($request->has('deleted_images')) {
            foreach ($request->deleted_images as $pathToDelete) {
                $relativePath = 'projects/'.basename($pathToDelete);
                if (($key = array_search($relativePath, $currentImages)) !== false) {
                    Storage::disk('public')->delete($relativePath);
                    unset($currentImages[$key]);
                }
            }
            $currentImages = array_values($currentImages);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $fileOriginalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $fileName = 'project_'.$fileOriginalName.'_'.uniqid().'.'.$extension;
                $currentImages[] = $file->storeAs('projects', $fileName, 'public');
            }
        }

        $slug = $project->slug;
        if ($request->has('slug') && $request->slug != $project->slug) {
            $slug = str()->slug($request->slug);
            if (Project::where('slug', $slug)->where('id', '!=', $project->id)->exists()) {
                $slug .= '-'.uniqid();
            }
        }

        $project->update([
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'category' => $request->category,
            'tech_stack' => $request->tech_stack,
            'live_url' => $request->live_url,
            'repo_url' => $request->repo_url,
            'is_featured' => $request->is_featured ?? false,
            'sort_order' => $request->sort_order ?? $project->sort_order,
            'images' => $currentImages,
        ]);
        Cache::forget('portfolio_projects');

        return $this->successResponse(
            new ProjectResource($project),
            'Project updated successfully.'
        );
    }

    public function destroy(string $id)
    {
        $project = Project::withoutTrashed()->findOrFail($id);
        $project->delete();
        Cache::forget('portfolio_projects');
        Cache::forget('portfolio_projects_archived');

        return $this->successResponse(
            [],
            'Project Archived successfully.'
        );
    }

    public function restore(string $id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        $project->restore();
        Cache::forget('portfolio_projects');
        Cache::forget('portfolio_projects_archived');

        return $this->successResponse(
            new ProjectResource($project),
            'Project restored successfully.'
        );
    }

    public function forceDelete(string $id)
    {
        $project = Project::findOrFail($id);

        foreach ($project->images as $pathToDelete) {
            $relativePath = 'projects/'.basename($pathToDelete);
            Storage::disk('public')->delete($relativePath);
        }
        $project->forceDelete();
        Cache::forget('portfolio_projects');
        Cache::forget('portfolio_projects_archived');

        return $this->successResponse(
            [],
            'Project deleted successfully.'
        );
    }
}
