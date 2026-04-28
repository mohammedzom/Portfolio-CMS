<?php

namespace App\Actions\SkillCategory;

use App\Models\SkillCategory;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSkillCategoryAction
{
    use AsAction;

    public function handle(SkillCategory $skillCategory, array $data): SkillCategory
    {
        $skillCategory->update($data);

        Cache::forget('portfolio_all');

        return $skillCategory;
    }
}
