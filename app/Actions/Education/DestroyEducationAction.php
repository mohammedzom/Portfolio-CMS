<?php

namespace App\Actions\Education;

use App\Models\Education;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class DestroyEducationAction
{
    use AsAction, ManagesCache;

    public function handle(Education $education): void
    {
        $education->delete();

        $this->forgetEducationCache();
    }
}
