<?php

namespace App\Actions\Services;

use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateServiceAction
{
    use AsAction;

    public function handle(Service $service, array $data): Service
    {
        $service->update($data);

        Cache::forget('services');
        Cache::forget('portfolio_all');

        return $service;
    }
}
