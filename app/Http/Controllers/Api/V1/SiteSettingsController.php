<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\UpdateSiteSettingsAction;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\UpdateSiteSettingsRequest;
use App\Http\Resources\SiteSettingstResource;
use App\Models\SiteSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SiteSettingsController extends Controller
{
    public function index(): JsonResponse
    {
        $hours = intval(config('app.cache_ttl_hours', 24));
        $ttl = now()->addHours($hours);

        $settings = Cache::remember('portfolio_settings', $ttl, function () {
            return $this->resolveForCache(new SiteSettingstResource(SiteSettings::firstOrFail()));
        });

        return $this->successResponse(
            $settings,
            'Site settings fetched successfully.'
        );
    }

    public function update(UpdateSiteSettingsRequest $request, UpdateSiteSettingsAction $action): JsonResponse
    {
        $settings = SiteSettings::firstOrCreate();

        $settings = $action->execute($settings, $request->validated(), $request->allFiles());

        Cache::forget('portfolio_settings');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new SiteSettingstResource($settings),
            'Site settings updated successfully.'
        );
    }
}
