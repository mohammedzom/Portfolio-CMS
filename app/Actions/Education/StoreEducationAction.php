<?php

namespace App\Actions\Education;

use App\Models\Education;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreEducationAction
{
    use AsAction, ManagesCache;

    public function handle(array $data): Education
    {
        $education = Education::create($data);

        $this->forgetEducationCache();

        return $education;
    }
}
