<?php

namespace App\Actions\Education;

use App\Models\Education;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreEducationAction
{
    use AsAction;

    public function handle(array $data): Education
    {
        $education = Education::create($data);

        Cache::forget('educations');
        Cache::forget('portfolio_all');

        return $education;
    }
}
