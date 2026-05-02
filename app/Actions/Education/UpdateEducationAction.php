<?php

namespace App\Actions\Education;

use App\Models\Education;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateEducationAction
{
    use AsAction, ManagesCache;

    public function handle(Education $education, array $data): Education
    {
        $education->update($data);

        $this->forgetEducationCache();

        return $education;
    }
}
