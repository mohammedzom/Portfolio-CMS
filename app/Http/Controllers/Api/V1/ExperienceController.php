<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Experiences\DestroyExperienceAction;
use App\Actions\Experiences\ForceDeleteExperienceAction;
use App\Actions\Experiences\RestoreExperienceAction;
use App\Actions\Experiences\StoreExperienceAction;
use App\Actions\Experiences\UpdateExperienceAction;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Experiences\StoreExperienceRequest;
use App\Http\Requests\Experiences\UpdateExperienceRequest;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExperienceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Experience::query();

        $cacheKey = 'experiences';

        if ($request->boolean('archived')) {
            $query->onlyTrashed();
            $cacheKey .= '_archived';
        }

        if ($request->filled('search')) {
            $cacheKey = null;
            $search = $request->string('search')->toString();
            $query->where(function ($query) use ($search): void {
                $query->where('job_title', 'like', '%'.$search.'%')
                    ->orWhere('company', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }
        $hours = intval(config('app.cache_ttl_hours', 24));
        $ttl = now()->addHours($hours);

        $resolver = fn () => $this->resolveForCache(ExperienceResource::collection($query->orderBy('start_date', 'desc')->get()));
        $experiences = $cacheKey
            ? Cache::remember($cacheKey, $ttl, $resolver)
            : $resolver();

        return $this->successResponse(
            $experiences,
            'Experiences retrieved successfully'
        );
    }

    public function store(StoreExperienceRequest $request): JsonResponse
    {
        $experience = StoreExperienceAction::run($request->validated());

        return $this->successResponse(
            new ExperienceResource($experience),
            'Experience created successfully.',
            201
        );
    }

    public function show(Experience $experience): JsonResponse
    {
        return $this->successResponse(
            new ExperienceResource($experience),
            'Experience retrieved successfully.'
        );
    }

    public function update(UpdateExperienceRequest $request, Experience $experience): JsonResponse
    {
        $experience = UpdateExperienceAction::run($experience, $request->validated());

        return $this->successResponse(
            new ExperienceResource($experience),
            'Experience updated successfully.'
        );
    }

    public function destroy(Experience $experience): JsonResponse
    {
        DestroyExperienceAction::run($experience);

        return $this->successResponse(
            [],
            'Experience Archived successfully.'
        );
    }

    public function restore(Experience $experience): JsonResponse
    {
        $experience = RestoreExperienceAction::run($experience);

        return $this->successResponse(
            new ExperienceResource($experience),
            'Experience restored successfully.'
        );
    }

    public function forceDelete(Experience $experience): JsonResponse
    {
        ForceDeleteExperienceAction::run($experience);

        return $this->successResponse(
            [],
            'Experience deleted permanently.'
        );
    }
}
