<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Education\StoreEducationRequest;
use App\Http\Resources\EducationResource;
use App\Models\Education;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EducationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'educations';
        $query = Education::orderBy('start_year', 'desc');
        if ($request->has('archived') && $request->input('archived') === true) {
            $cacheKey .= '_archived';
            $query->withTrashed();
        }

        $hours = intval(config('app.cache_ttl_hours', 24));
        $ttl = now()->addHours($hours);

        $educations = Cache::remember($cacheKey, $ttl, function () use ($query) {
            return $this->resolveForCache($query->get());
        });

        return $this->successResponse(
            EducationResource::collection($educations),
            'Education index fetched successfully.'
        );
    }

    public function store(StoreEducationRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $education = Education::create($validated);

        Cache::forget('educations');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new EducationResource($education),
            'Education created successfully.'
        );
    }

    public function show(string $id): JsonResponse
    {
        $education = Education::findOrFail($id);

        return $this->successResponse(
            new EducationResource($education),
            'Education fetched successfully.'
        );
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validated();
        $education = Education::findOrFail($id);
        $education->update($validated);

        Cache::forget('educations');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new EducationResource($education),
            'Education updated successfully.'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $education = Education::withoutTrashed()->findOrFail($id);
        $education->delete();

        Cache::forget('educations_archive');
        Cache::forget('educations');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            null,
            'Education Archived successfully.'
        );
    }

    public function restore(string $id): JsonResponse
    {
        $education = Education::onlyTrashed()->findOrFail($id);
        $education->restore();

        Cache::forget('educations_archive');
        Cache::forget('educations');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new EducationResource($education),
            'Education restored successfully.'
        );
    }

    public function forceDelete(string $id): JsonResponse
    {
        $education = Education::withTrashed()->findOrFail($id);
        $isTrashed = $education->trashed();
        $education->forceDelete();

        if ($isTrashed) {
            Cache::forget('educations_archive');
        } else {
            Cache::forget('educations');
            Cache::forget('portfolio_all');
        }

        return $this->successResponse(
            null,
            'Education deleted permanently successfully.'
        );
    }
}
