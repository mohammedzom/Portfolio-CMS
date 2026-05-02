<?php

namespace App\Actions\SkillCategory;

use App\Models\SkillCategory;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSkillCategoryAction
{
    use AsAction, ManagesCache;

    public function handle(SkillCategory $skillCategory, array $data): SkillCategory
    {
        $skillCategory->update($data);

        $this->forgetPortfolioCache();

        return $skillCategory;
    }
}
