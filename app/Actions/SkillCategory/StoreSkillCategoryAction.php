<?php

namespace App\Actions\SkillCategory;

use App\Models\SkillCategory;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreSkillCategoryAction
{
    use AsAction;

    public function handle(array $data): SkillCategory
    {
        $category = SkillCategory::create($data);

        Cache::forget('portfolio_all');

        return $category;
    }
}
