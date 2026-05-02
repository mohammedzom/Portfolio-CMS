<?php

namespace App\Actions\Services;

use App\Models\Service;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreServiceAction
{
    use AsAction, ManagesCache;

    public function handle(Service $service): Service
    {
        $service->restore();

        $this->forgetServicesCache();

        return $service;
    }
}
