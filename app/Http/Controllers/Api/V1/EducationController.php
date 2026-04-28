<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Education\DestroyEducationAction;
use App\Actions\Education\ForceDeleteEducationAction;
use App\Actions\Education\RestoreEducationAction;
use App\Actions\Education\StoreEducationAction;
use App\Actions\Education\UpdateEducationAction;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Education\StoreEducationRequest;
use App\Http\Requests\Education\UpdateEducationRequest;
use App\Http\Resources\EducationResource;
use App\Models\Education;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EducationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Education::orderBy('start_year', 'desc');
        $cacheKey = 'educations';

        if ($request->boolean('archived')) {
            $query->onlyTrashed();
            $cacheKey .= '_archived';
        }

        $hours = intval(config('app.cache_ttl_hours', 24));
        $ttl = now()->addHours($hours);

        $educations = Cache::remember($cacheKey, $ttl, function () use ($query) {
            return $this->resolveForCache(EducationResource::collection($query->get()));
        });

        return $this->successResponse(
            $educations,
            'Education index fetched successfully.'
        );
    }

    public function store(StoreEducationRequest $request): JsonResponse
    {
        $education = StoreEducationAction::run($request->validated());

        return $this->successResponse(
            new EducationResource($education),
            'Education created successfully.'
        );
    }

    public function show(Education $education): JsonResponse
    {
        return $this->successResponse(
            new EducationResource($education),
            'Education fetched successfully.'
        );
    }

    public function update(UpdateEducationRequest $request, Education $education): JsonResponse
    {
        $education = UpdateEducationAction::run($education, $request->validated());

        return $this->successResponse(
            new EducationResource($education),
            'Education updated successfully.'
        );
    }

    public function destroy(Education $education): JsonResponse
    {
        DestroyEducationAction::run($education);

        return $this->successResponse(
            null,
            'Education Archived successfully.'
        );
    }

    public function restore(Education $education): JsonResponse
    {
        $education = RestoreEducationAction::run($education);

        return $this->successResponse(
            new EducationResource($education),
            'Education restored successfully.'
        );
    }

    public function forceDelete(Education $education): JsonResponse
    {
        ForceDeleteEducationAction::run($education);

        return $this->successResponse(
            [],
            'Education deleted permanently.'
        );
    }
}
