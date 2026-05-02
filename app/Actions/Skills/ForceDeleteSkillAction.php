<?php

namespace App\Actions\Skills;

use App\Models\Skill;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class ForceDeleteSkillAction
{
    use AsAction, ManagesCache;

    public function handle(Skill $skill): void
    {
        $skill->forceDelete();

        $this->forgetSkillsCache();
    }
}
