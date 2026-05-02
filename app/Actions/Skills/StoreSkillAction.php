<?php

namespace App\Actions\Skills;

use App\Models\Skill;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreSkillAction
{
    use AsAction, ManagesCache;

    public function handle(array $data): Skill
    {
        $skill = Skill::create($data);

        $this->forgetSkillsCache();

        return $skill->load('category');
    }
}
