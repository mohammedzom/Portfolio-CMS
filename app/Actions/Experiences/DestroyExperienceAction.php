<?php

namespace App\Actions\Experiences;

use App\Models\Experience;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class DestroyExperienceAction
{
    use AsAction;

    public function handle(Experience $experience): void
    {
        $experience->delete();

        Cache::forget('experiences');
        Cache::forget('experiences_archived');
        Cache::forget('portfolio_all');
    }
}
