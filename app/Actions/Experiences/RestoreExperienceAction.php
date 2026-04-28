<?php

namespace App\Actions\Experiences;

use App\Models\Experience;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreExperienceAction
{
    use AsAction;

    public function handle(Experience $experience): Experience
    {
        $experience->restore();

        Cache::forget('experiences');
        Cache::forget('experiences_archived');
        Cache::forget('portfolio_all');

        return $experience;
    }
}
