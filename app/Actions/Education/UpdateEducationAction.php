<?php

namespace App\Actions\Education;

use App\Models\Education;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateEducationAction
{
    use AsAction;

    public function handle(Education $education, array $data): Education
    {
        $education->update($data);

        Cache::forget('educations');
        Cache::forget('portfolio_all');

        return $education;
    }
}
