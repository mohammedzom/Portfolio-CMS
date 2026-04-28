<?php

namespace App\Actions\Skills;

use App\Models\Skill;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class DestroySkillAction
{
    use AsAction;

    public function handle(Skill $skill): void
    {
        $skill->delete();

        Cache::forget('portfolio_all');
    }
}
