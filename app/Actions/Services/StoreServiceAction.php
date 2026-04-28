<?php

namespace App\Actions\Services;

use App\Models\Service;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreServiceAction
{
    use AsAction;

    public function handle(array $data): Service
    {
        $service = Service::create($data);

        Cache::forget('services');
        Cache::forget('portfolio_all');

        return $service;
    }
}
