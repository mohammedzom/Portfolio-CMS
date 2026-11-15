<?php

namespace App\Actions\Experiences;

use App\Models\Experience;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class ForceDeleteExperienceAction
{
    use AsAction, ManagesCache;

    public function handle(Experience $experience): void
    {
        $experience->forceDelete();

        $this->forgetExperiencesCache();
    }
}
