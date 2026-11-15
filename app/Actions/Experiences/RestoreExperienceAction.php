<?php

namespace App\Actions\Experiences;

use App\Models\Experience;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreExperienceAction
{
    use AsAction, ManagesCache;

    public function handle(Experience $experience): Experience
    {
        $experience->restore();

        $this->forgetExperiencesCache();

        return $experience;
    }
}
