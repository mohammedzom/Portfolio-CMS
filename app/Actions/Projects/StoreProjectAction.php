<?php

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreProjectAction
{
    use AsAction;

    public function handle(array $data, array $files = []): Project
    {
        return DB::transaction(function () use ($data, $files) {
            $paths = [];
            foreach ($files as $file) {
                $fileOriginalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $fileName = 'project_'.$fileOriginalName.'_'.uniqid().'.'.$extension;
                $paths[] = $file->storeAs('projects', $fileName, 'public');
            }

            $slug = $data['slug'] ?? str()->slug($data['title']);
            if (Project::where('slug', $slug)->exists()) {
                $slug .= '-'.uniqid();
            }

            $maxSortOrder = Project::withoutTrashed()->max('sort_order') + 1;

            $project = Project::create([
                'title' => $data['title'],
                'slug' => $slug,
                'description' => $data['description'],
                'category' => $data['category'],
                'tech_stack' => $data['tech_stack'] ?? null,
                'live_url' => $data['live_url'] ?? null,
                'repo_url' => $data['repo_url'] ?? null,
                'is_featured' => $data['is_featured'] ?? false,
                'sort_order' => $data['sort_order'] ?? $maxSortOrder,
                'images' => $paths,
            ]);

            Cache::forget('portfolio_projects');
            Cache::forget('portfolio_all');

            return $project;
        });
    }
}
