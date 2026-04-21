<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();

        if ($request->has('archived') && $request->archived) {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
            $query->orWhere('description', 'like', '%'.$request->search.'%');
        }
        $projects = $query->orderBy('sort_order', 'desc');

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
        $project = Project::create([
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'category' => $request->category,
            'tech_stack' => $request->tech_stack,
            'live_url' => $request->live_url,
            'repo_url' => $request->repo_url,
            'is_featured' => $request->is_featured ?? false,
            'sort_order' => $request->sort_order,
            'images' => $paths,
        ]);

        return $this->successResponse(
            new ProjectResource($project),
            'Project created successfully.',
            201
        );
    }

    public function update(UpdateProjectRequest $request, string $id)
    {
        $project = Project::withoutTrashed()->findOrFail($id);
        $validated = $request->validated();

        $paths = is_array($project->images) ? $project->images : [];
        if ($validated->hasFile('images')) {
            foreach ($validated->file('images') as $file) {
                $fileOriginalName = explode('.', $file->getClientOriginalName())[0];
                $extension = $file->getClientOriginalExtension();
                $fileName = 'project_'.$fileOriginalName.'_'.uniqid().'.'.$extension;
                $paths[] = $file->storeAs('projects', $fileName, 'public');
            }
        }

        $slug = $request->slug ?? str()->slug($request->title);
        if (Project::where('slug', $slug)->where('id', '!=', $project->id)->exists()) {
            $slug .= '-'.uniqid();
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
            'sort_order' => $request->sort_order,
            'images' => $paths,
        ]);

        return $this->successResponse(
            new ProjectResource($project),
            'Project updated successfully.'
        );
    }

    public function destroy(string $id)
    {
        $project = Project::withoutTrashed()->findOrFail($id);
        $project->delete();

        return $this->successResponse(
            [],
            'Project deleted successfully.',
            204
        );
    }

    public function restore(string $id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        $project->restore();

        return $this->successResponse(
            new ProjectResource($project),
            'Project restored successfully.'
        );
    }

    public function forceDelete(string $id)
    {
        $project = Project::withTrashed()->findOrFail($id);
        $project->forceDelete();

        return $this->successResponse(
            [],
            'Project force deleted successfully.',
            204
        );
    }
}
