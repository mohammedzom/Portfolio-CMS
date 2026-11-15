<?php

namespace App\Actions\Education;

use App\Models\Education;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreEducationAction
{
    use AsAction, ManagesCache;

    public function handle(Education $education): Education
    {
        $education->restore();

        $this->forgetEducationCache();

        return $education;
    }
}
