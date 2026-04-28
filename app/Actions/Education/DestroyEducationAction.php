<?php

namespace App\Actions\Education;

use App\Models\Education;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class DestroyEducationAction
{
    use AsAction;

    public function handle(Education $education): void
    {
        $education->delete();

        Cache::forget('educations');
        Cache::forget('educations_archived');
        Cache::forget('portfolio_all');
    }
}
