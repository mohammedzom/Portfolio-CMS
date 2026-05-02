<?php

namespace App\Actions\Services;

use App\Models\Service;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreServiceAction
{
    use AsAction, ManagesCache;

    public function handle(array $data): Service
    {
        $service = Service::create($data);

        $this->forgetServicesCache();

        return $service;
    }
}
