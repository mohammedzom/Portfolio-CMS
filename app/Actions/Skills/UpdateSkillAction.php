<?php

namespace App\Actions\Skills;

use App\Models\Skill;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSkillAction
{
    use AsAction, ManagesCache;

    public function handle(Skill $skill, array $data): Skill
    {
        $skill->update($data);

        $this->forgetSkillsCache();

        return $skill->load('category');
    }
}
