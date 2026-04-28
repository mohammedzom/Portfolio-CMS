<?php

namespace App\Actions\Education;

use App\Models\Education;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreEducationAction
{
    use AsAction;

    public function handle(Education $education): Education
    {
        $education->restore();

        Cache::forget('educations');
        Cache::forget('educations_archived');
        Cache::forget('portfolio_all');

        return $education;
    }
}
