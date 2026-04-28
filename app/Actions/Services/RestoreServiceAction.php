<?php

namespace App\Actions\Services;

use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreServiceAction
{
    use AsAction;

    public function handle(Service $service): Service
    {
        $service->restore();

        Cache::forget('services');
        Cache::forget('services_archived');
        Cache::forget('portfolio_all');

        return $service;
    }
}
