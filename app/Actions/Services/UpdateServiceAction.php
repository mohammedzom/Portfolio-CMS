<?php

namespace App\Actions\Services;

use App\Models\Service;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateServiceAction
{
    use AsAction, ManagesCache;

    public function handle(Service $service, array $data): Service
    {
        $service->update($data);

        $this->forgetServicesCache();

        return $service;
    }
}
