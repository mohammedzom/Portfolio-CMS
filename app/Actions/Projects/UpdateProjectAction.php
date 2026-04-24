<?php

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateProjectAction
{
    use AsAction;

    public function handle(Project $project, array $data, array $deletedImages = [], array $newFiles = []): Project
    {
        return DB::transaction(function () use ($project, $data, $deletedImages, $newFiles) {
            $currentImages = is_array($project->images) ? $project->images : [];

            if (! empty($deletedImages)) {
                foreach ($deletedImages as $pathToDelete) {
                    $relativePath = 'projects/'.basename($pathToDelete);
                    if (($key = array_search($relativePath, $currentImages)) !== false) {
                        Storage::disk('public')->delete($relativePath);
                        unset($currentImages[$key]);
                    }
                }
                $currentImages = array_values($currentImages);
            }

            foreach ($newFiles as $file) {
                $fileOriginalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $fileName = 'project_'.$fileOriginalName.'_'.uniqid().'.'.$extension;
                $currentImages[] = $file->storeAs('projects', $fileName, 'public');
            }

            $slug = $project->slug;
            if (isset($data['slug']) && $data['slug'] != $project->slug) {
                $slug = str()->slug($data['slug']);
                if (Project::where('slug', $slug)->where('id', '!=', $project->id)->exists()) {
                    $slug .= '-'.uniqid();
                }
            }

            $project->update([
                'title' => $data['title'] ?? $project->title,
                'slug' => $slug,
                'description' => $data['description'] ?? $project->description,
                'category' => $data['category'] ?? $project->category,
                'tech_stack' => array_key_exists('tech_stack', $data) ? $data['tech_stack'] : $project->tech_stack,
                'live_url' => array_key_exists('live_url', $data) ? $data['live_url'] : $project->live_url,
                'repo_url' => array_key_exists('repo_url', $data) ? $data['repo_url'] : $project->repo_url,
                'is_featured' => array_key_exists('is_featured', $data) ? $data['is_featured'] : $project->is_featured,
                'sort_order' => array_key_exists('sort_order', $data) ? $data['sort_order'] : $project->sort_order,
                'images' => $currentImages,
            ]);

            Cache::forget('portfolio_projects');
            Cache::forget('portfolio_all');

            return $project;
        });
    }
}
