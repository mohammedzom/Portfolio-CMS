<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $query = Project::query();

        if (request()->has('archived') && request('archived')) {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }
        if (request('search')) {
            $query->where('title', 'like', '%'.request('search').'%');
            $query->orWhere('description', 'like', '%'.request('search').'%');
        }
        $projects = $query->orderBy('sort_order', 'desc')->paginate(10);

        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        $projectsCount = Project::count();

        return view('admin.projects.create', compact('projectsCount'));
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

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully');
    }

    public function edit(Project $project)
    {
        $projectsCount = Project::count();

        return view('admin.projects.edit', compact('project', 'projectsCount'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $validated = $request->validated();

        $paths = is_array($project->images) ? $project->images : [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
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

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project moved to trash');
    }

    public function restore($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        $project->restore();

        return redirect()->route('admin.projects.index', ['archived' => 'true'])
            ->with('success', 'Project restored successfully');
    }
}
