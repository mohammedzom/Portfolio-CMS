<?php

namespace App\Actions\SkillCategory;

use App\Models\SkillCategory;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreSkillCategoryAction
{
    use AsAction, ManagesCache;

    public function handle(array $data): SkillCategory
    {
        $category = SkillCategory::create($data);

        $this->forgetPortfolioCache();

        return $category;
    }
}
