<?php

namespace App\Actions\Services;

use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class ForceDeleteServiceAction
{
    use AsAction;

    public function handle(Service $service): void
    {
        $service->forceDelete();

        Cache::forget('services');
        Cache::forget('services_archived');
        Cache::forget('portfolio_all');
    }
}
