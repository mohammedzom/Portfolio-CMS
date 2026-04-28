<?php

namespace App\Actions\Services;

use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class DestroyServiceAction
{
    use AsAction;

    public function handle(Service $service): void
    {
        $service->delete();

        Cache::forget('services');
        Cache::forget('services_archived');
        Cache::forget('portfolio_all');
    }
}
