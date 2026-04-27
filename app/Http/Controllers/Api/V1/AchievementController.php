<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Achievement\StoreAchievementAction;
use App\Actions\Achievement\UpdateAchievementAction;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Achievement\StoreAchievementRequest;
use App\Http\Requests\Achievement\UpdateAchievementRequest;
use App\Http\Resources\AchievementResource;
use App\Models\Achievement;
use App\Traits\ManageSoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AchievementController extends Controller
{
    use ManageSoftDeletes;

    protected $modelClass = Achievement::class;

    protected $resourceClass = AchievementResource::class;

    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'achievements';
        $query = Achievement::orderBy('date', 'desc');
        if ($request->has('archived') && $request->input('archived') === true) {
            $query->onlyTrashed();
        }

        $hours = intval(config('app.cache_ttl_hours', 24));
        $ttl = now()->addHours($hours);

        $achievements = Cache::remember($cacheKey, $ttl, function () use ($query) {
            return $this->resolveForCache(AchievementResource::collection($query->get()));
        });

        return $this->successResponse(
            $achievements,
            'Achievement index fetched successfully.'
        );
    }

    public function store(StoreAchievementRequest $request): JsonResponse
    {
        $achievement = StoreAchievementAction::run(
            $request->validated(),
            $request->hasFile('file') ? $request->file('file') : null
        );

        return $this->successResponse(
            new AchievementResource($achievement),
            'Achievement created successfully.',
            201
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
        $achievement = UpdateAchievementAction::run(
            $request->validated(),
            $request->hasFile('file') ? $request->file('file') : null,
            $id
        );

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

        Cache::forget('achievements');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            null,
            'Achievement Archived successfully.'
        );
    }

    public function afterRestore(Achievement $achievement): void
    {
        Cache::forget('achievements');
        Cache::forget('portfolio_all');
    }

    protected function beforeForceDelete(Achievement $achievement): void
    {
        if ($achievement->certificate_url) {
            Storage::disk('public')->delete($achievement->certificate_url);
        }
    }

    protected function afterForceDelete(): void
    {
        Cache::forget('achievements');
        Cache::forget('portfolio_all');
    }
}
