<?php

namespace App\Actions\Projects;

use App\Models\Project;
use App\Traits\ManagesCache;
use App\Traits\ManagesUploadedFiles;
use Illuminate\Http\UploadedFile;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreProjectAction
{
    use AsAction, ManagesCache, ManagesUploadedFiles;

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, UploadedFile>  $files
     */
    public function handle(array $data, array $files = []): Project
    {
        $imagePaths = $this->storeUploadedFiles($files, 'projects');

        $project = $this->commitFileChanges(function () use ($data, $imagePaths): Project {
            $slug = $data['slug'] ?? str()->slug($data['title']);
            if (Project::where('slug', $slug)->exists()) {
                $slug .= '-'.uniqid();
            }

            $maxSortOrder = Project::withoutTrashed()->max('sort_order') + 1;

            return Project::create([
                'title' => $data['title'],
                'slug' => $slug,
                'description' => $data['description'],
                'category' => $data['category'],
                'tech_stack' => $data['tech_stack'] ?? null,
                'live_url' => $data['live_url'] ?? null,
                'repo_url' => $data['repo_url'] ?? null,
                'is_featured' => $data['is_featured'] ?? false,
                'sort_order' => $data['sort_order'] ?? $maxSortOrder,
                'images' => $imagePaths,
            ]);
        }, newPaths: $imagePaths);

        $this->forgetProjectCache($project->slug);

        return $project;
    }
}
