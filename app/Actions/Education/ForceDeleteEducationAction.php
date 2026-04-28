<?php

namespace App\Actions\Education;

use App\Models\Education;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class ForceDeleteEducationAction
{
    use AsAction;

    public function handle(Education $education): void
    {
        $education->forceDelete();

        Cache::forget('educations');
        Cache::forget('educations_archived');
        Cache::forget('portfolio_all');
    }
}
