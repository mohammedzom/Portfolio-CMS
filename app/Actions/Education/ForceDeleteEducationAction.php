<?php

namespace App\Actions\Education;

use App\Models\Education;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class ForceDeleteEducationAction
{
    use AsAction, ManagesCache;

    public function handle(Education $education): void
    {
        $education->forceDelete();

        $this->forgetEducationCache();
    }
}
