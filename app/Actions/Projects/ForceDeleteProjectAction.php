<?php

namespace App\Actions\Projects;

use App\Models\Project;
use App\Traits\ManagesCache;
use App\Traits\ManagesUploadedFiles;
use Lorisleiva\Actions\Concerns\AsAction;

class ForceDeleteProjectAction
{
    use AsAction, ManagesCache, ManagesUploadedFiles;

    public function handle(Project $project): void
    {
        $images = is_array($project->images) ? array_values($project->images) : [];

        $this->commitFileChanges(
            fn () => $project->forceDelete(),
            obsoletePaths: $images,
        );

        $this->forgetProjectCache($project->slug);
    }
}
