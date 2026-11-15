<?php

namespace App\Actions\Projects;

use App\Models\Project;
use App\Traits\ManagesCache;
use App\Traits\ManagesUploadedFiles;
use Illuminate\Http\UploadedFile;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateProjectAction
{
    use AsAction, ManagesCache, ManagesUploadedFiles;

    /**
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $deletedImages  Stored paths or public URLs of images to remove.
     * @param  array<int, UploadedFile>  $newFiles
     */
    public function handle(Project $project, array $data, array $deletedImages = [], array $newFiles = []): Project
    {
        $previousSlug = $project->slug;
        $currentImages = is_array($project->images) ? array_values($project->images) : [];
        $removedImages = $this->resolveRemovedImages($currentImages, $deletedImages);
        $newImagePaths = $this->storeUploadedFiles($newFiles, 'projects');

        $this->commitFileChanges(function () use ($project, $data, $currentImages, $removedImages, $newImagePaths): void {
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
                'images' => [...array_values(array_diff($currentImages, $removedImages)), ...$newImagePaths],
            ]);
        }, newPaths: $newImagePaths, obsoletePaths: $removedImages);

        $this->forgetProjectCache($previousSlug);
        $this->forgetProjectCache($project->slug);

        return $project;
    }

    /**
     * Map requested deletions onto images that actually belong to the project.
     *
     * @param  list<string>  $currentImages
     * @param  array<int, string>  $deletedImages
     * @return list<string>
     */
    private function resolveRemovedImages(array $currentImages, array $deletedImages): array
    {
        $requestedPaths = array_map(
            fn (string $image): string => 'projects/'.basename($image),
            $deletedImages,
        );

        return array_values(array_intersect($currentImages, $requestedPaths));
    }
}
