<?php

namespace App\Actions\Experiences;

use App\Models\Experience;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class DestroyExperienceAction
{
    use AsAction, ManagesCache;

    public function handle(Experience $experience): void
    {
        $experience->delete();

        $this->forgetExperiencesCache();
    }
}
