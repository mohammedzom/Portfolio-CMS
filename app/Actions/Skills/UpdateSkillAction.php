<?php

namespace App\Actions\Skills;

use App\Models\Skill;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSkillAction
{
    use AsAction;

    public function handle(Skill $skill, array $data): Skill
    {
        $skill->update($data);

        Cache::forget('portfolio_all');

        return $skill->load('category');
    }
}
