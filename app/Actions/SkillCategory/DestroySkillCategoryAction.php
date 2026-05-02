<?php

namespace App\Actions\SkillCategory;

use App\Models\SkillCategory;
use App\Traits\ManagesCache;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class DestroySkillCategoryAction
{
    use AsAction, ManagesCache;

    public function handle(SkillCategory $skillCategory): void
    {
        DB::transaction(function () use ($skillCategory): void {
            if ($skillCategory->skills()->exists()) {
                $skillCategory->skills()->delete();
            }

            $skillCategory->delete();
        });

        $this->forgetPortfolioCache();
    }
}
