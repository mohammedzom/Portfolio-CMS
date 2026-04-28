<?php

namespace App\Actions\SkillCategory;

use App\Models\SkillCategory;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class ForceDeleteSkillCategoryAction
{
    use AsAction;

    public function handle(SkillCategory $skillCategory): void
    {
        $skillCategory->forceDelete();

        Cache::forget('portfolio_all');
    }
}
