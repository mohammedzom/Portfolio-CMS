<?php

namespace App\Actions\Skills;

use App\Models\Skill;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class ForceDeleteSkillAction
{
    use AsAction;

    public function handle(Skill $skill): void
    {
        $skill->forceDelete();

        Cache::forget('portfolio_all');
    }
}
