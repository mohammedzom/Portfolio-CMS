<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Achievement\StoreAchievementRequest;
use App\Http\Requests\Achievement\UpdateAchievementRequest;
use App\Http\Resources\AchievementResource;
use App\Models\Achievement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AchievementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'achievements';
        $query = Achievement::orderBy('date', 'desc');
        if ($request->has('archived') && $request->input('archived') === true) {
            $cacheKey .= '_archived';
            $query->withTrashed();
        }

        $hours = intval(config('app.cache_ttl_hours', 24));
        $ttl = now()->addHours($hours);

        $achievements = Cache::remember($cacheKey, $ttl, function () use ($query) {
            return $this->resolveForCache($query->get());
        });

        return $this->successResponse(
            AchievementResource::collection($achievements),
            'Achievement index fetched successfully.'
        );
    }

    public function store(StoreAchievementRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $achievement = Achievement::create($validated);

        Cache::forget('achievements');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new AchievementResource($achievement),
            'Achievement created successfully.'
        );
    }

    public function show(string $id): JsonResponse
    {
        $achievement = Achievement::findOrFail($id);

        return $this->successResponse(
            new AchievementResource($achievement),
            'Achievement fetched successfully.'
        );
    }

    public function update(UpdateAchievementRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();
        $achievement = Achievement::findOrFail($id);
        $achievement->update($validated);

        Cache::forget('achievements');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new AchievementResource($achievement),
            'Achievement updated successfully.'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $achievement = Achievement::withoutTrashed()->findOrFail($id);
        $achievement->delete();

        Cache::forget('achievements_archive');
        Cache::forget('achievements');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            null,
            'Achievement Archived successfully.'
        );
    }

    public function restore(string $id): JsonResponse
    {
        $achievement = Achievement::onlyTrashed()->findOrFail($id);
        $achievement->restore();

        Cache::forget('achievements_archive');
        Cache::forget('achievements');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new AchievementResource($achievement),
            'Achievement restored successfully.'
        );
    }

    public function forceDelete(string $id): JsonResponse
    {
        $achievement = Achievement::withTrashed()->findOrFail($id);
        $isTrashed = $achievement->trashed();
        $achievement->forceDelete();

        if ($isTrashed) {
            Cache::forget('achievements_archive');
        } else {
            Cache::forget('achievements');
            Cache::forget('portfolio_all');
        }

        return $this->successResponse(
            null,
            'Achievement deleted permanently successfully.'
        );
    }
}
