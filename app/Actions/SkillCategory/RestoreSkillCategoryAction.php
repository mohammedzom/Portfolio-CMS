<?php

namespace App\Actions\SkillCategory;

use App\Models\SkillCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreSkillCategoryAction
{
    use AsAction;

    public function handle(SkillCategory $skillCategory): SkillCategory
    {
        DB::transaction(function () use ($skillCategory): void {
            $deletedAt = $skillCategory->deleted_at;

            $skillCategory->restore();

            if ($deletedAt !== null) {
                $skillCategory->skills()
                    ->onlyTrashed()
                    ->whereBetween('deleted_at', [
                        $deletedAt->copy()->subSeconds(5),
                        $deletedAt->copy()->addSeconds(5),
                    ])
                    ->restore();
            }
        });

        Cache::forget('portfolio_all');

        return $skillCategory->load('skills');
    }
}
