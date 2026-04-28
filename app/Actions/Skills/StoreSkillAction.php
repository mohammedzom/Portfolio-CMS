<?php

namespace App\Actions\Skills;

use App\Models\Skill;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreSkillAction
{
    use AsAction;

    public function handle(array $data): Skill
    {
        $skill = Skill::create($data);

        Cache::forget('portfolio_all');

        return $skill->load('category');
    }
}
