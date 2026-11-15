<?php

namespace App\Actions\Achievement;

use App\Models\Achievement;
use App\Traits\ManagesCache;
use App\Traits\ManagesUploadedFiles;
use Lorisleiva\Actions\Concerns\AsAction;

class ForceDeleteAchievementAction
{
    use AsAction, ManagesCache, ManagesUploadedFiles;

    public function handle(Achievement $achievement): void
    {
        $this->commitFileChanges(
            fn () => $achievement->forceDelete(),
            obsoletePaths: array_filter([$achievement->certificate_url]),
        );

        $this->forgetAchievementsCache();
    }
}
