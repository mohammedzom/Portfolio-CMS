<?php

namespace App\Actions\Skills;

use App\Exceptions\ApiException;
use App\Models\Skill;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreSkillAction
{
    use AsAction;

    public function handle(Skill $skill, ?int $newCategoryId = null): Skill
    {
        $skill->load(['category' => fn ($query) => $query->withTrashed()]);

        if ($skill->category?->trashed() && $newCategoryId === null) {
            throw ApiException::conflict(
                'Category is archived. Restore the category first or provide new_category_id.'
            );
        }

        if ($newCategoryId !== null) {
            $skill->update(['skill_category_id' => $newCategoryId]);
        }

        $skill->restore();

        Cache::forget('portfolio_all');

        return $skill->load('category');
    }
}
