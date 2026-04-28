<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Experiences\StoreExperienceRequest;
use App\Http\Requests\Experiences\UpdateExperienceRequest;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use App\Traits\ManageSoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class ExperienceController extends Controller
{
    use ManageSoftDeletes;

    protected $modelClass = Experience::class;

    protected $resourceClass = ExperienceResource::class;

    public function index(Request $request): JsonResponse
    {
        $query = Experience::query();

        $cacheKey = 'experiences';
        if ($request->has('archived') && $request->input('archived') == true) {
            $query->onlyTrashed();
            $cacheKey .= '_archived';
        }
        if ($request->filled('search')) {
            $cacheKey = null;
            $query->where('job_title', 'like', '%'.$request->search.'%')
                ->orWhere('company', 'like', '%'.$request->search.'%')
                ->orWhere('description', 'like', '%'.$request->search.'%');
        }
        $hours = intval(config('app.cache_ttl_hours', 24));
        $ttl = now()->addHours($hours);

        $experiences = Cache::remember($cacheKey, $ttl, function () use ($query) {
            return $this->resolveForCache(ExperienceResource::collection($query->orderBy('start_date', 'desc')->get()));
        });

        return $this->successResponse(
            $experiences,
            'Experiences retrieved successfully'
        );
    }

    public function store(StoreExperienceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $this->checkForbiddenFields($data);
        $experience = Experience::create($data);
        Cache::forget('experiences');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new ExperienceResource($experience),
            'Experience created successfully.',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $experience = Experience::findOrFail($id);

        return $this->successResponse(
            new ExperienceResource($experience),
            'Experience retrieved successfully.'
        );
    }

    public function update(UpdateExperienceRequest $request, string $id): JsonResponse
    {
        $experience = Experience::withoutTrashed()->findOrFail($id);
        $data = $request->validated();
        $this->checkForbiddenFields($data);
        $experience->update($data);
        Cache::forget('experiences');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new ExperienceResource($experience),
            'Experience updated successfully.'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $experience = Experience::withoutTrashed()->findOrFail($id);
        $experience->delete();
        Cache::forget('experiences');
        Cache::forget('experiences_archived');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            [],
            'Experience Archived successfully.'
        );
    }

    protected function afterRestore(): void
    {
        Cache::forget('experiences');
        Cache::forget('experiences_archived');
        Cache::forget('portfolio_all');
    }

    protected function afterForceDelete(): void
    {
        Cache::forget('experiences');
        Cache::forget('experiences_archived');
        Cache::forget('portfolio_all');
    }

    protected function checkForbiddenFields(array $data)
    {
        if ($data['is_current'] && isset($data['end_date'])) {
            throw ValidationException::withMessages(
                [
                    'end_date' => 'End date must be false when is_current is true.',
                ],
                'VALIDATION_ERROR'
            );
        }
    }
}
