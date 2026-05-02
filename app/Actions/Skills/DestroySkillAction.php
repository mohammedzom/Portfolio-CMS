<?php

namespace App\Actions\Skills;

use App\Models\Skill;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class DestroySkillAction
{
    use AsAction, ManagesCache;

    public function handle(Skill $skill): void
    {
        $skill->delete();

        $this->forgetSkillsCache();
    }
}
