<?php

namespace App\Actions\Services;

use App\Models\Service;
use App\Traits\ManagesCache;
use Lorisleiva\Actions\Concerns\AsAction;

class DestroyServiceAction
{
    use AsAction, ManagesCache;

    public function handle(Service $service): void
    {
        $service->delete();

        $this->forgetServicesCache();
    }
}
