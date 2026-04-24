<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Experiences\StoreExperienceRequest;
use App\Http\Requests\Experiences\UpdateExperienceRequest;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class ExperienceController extends Controller
{
    public function index(Request $request)
    {
        $query = Experience::query();
        if ($request->has('archived') && $request->archived) {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }
        if ($request->filled('search')) {
            $query->where('job_title', 'like', '%'.$request->search.'%')
                ->orWhere('company', 'like', '%'.$request->search.'%')
                ->orWhere('description', 'like', '%'.$request->search.'%');
        }
        $hours = intval(config('app.cache_ttl_hours', 24));
        $ttl = now()->addHours($hours);

        $experiences = Cache::remember('portfolio_experiences', $ttl, function () use ($query) {
            return $this->resolveForCache(ExperienceResource::collection($query->orderBy('start_date', 'desc')->get()));
        });

        return $this->successResponse(
            $experiences,
            'Experiences retrieved successfully'
        );
    }

    public function store(StoreExperienceRequest $request)
    {
        $data = $request->validated();
        $this->checkForbiddenFields($data);
        $experience = Experience::create($data);
        Cache::forget('portfolio_experiences');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new ExperienceResource($experience),
            'Experience created successfully.',
            201
        );
    }

    public function show(string $id)
    {
        $experience = Experience::findOrFail($id);

        return $this->successResponse(
            new ExperienceResource($experience),
            'Experience retrieved successfully.'
        );
    }

    public function update(UpdateExperienceRequest $request, string $id)
    {
        $experience = Experience::withoutTrashed()->findOrFail($id);
        $data = $request->validated();
        $this->checkForbiddenFields($data);
        $experience->update($data);
        Cache::forget('portfolio_experiences');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new ExperienceResource($experience),
            'Experience updated successfully.'
        );
    }

    public function destroy(string $id)
    {
        $experience = Experience::withoutTrashed()->findOrFail($id);
        $experience->delete();
        Cache::forget('portfolio_experiences');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            [],
            'Experience Archived successfully.'
        );
    }

    public function restore(string $id)
    {
        $experience = Experience::onlyTrashed()->findOrFail($id);
        $experience->restore();
        Cache::forget('portfolio_experiences');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new ExperienceResource($experience),
            'Experience restored successfully.'
        );
    }

    public function forceDelete(string $id)
    {
        $experience = Experience::withTrashed()->findOrFail($id);
        $experience->forceDelete();
        Cache::forget('portfolio_experiences');
        Cache::forget('portfolio_all');

        return $this->successResponse(
            [],
            'Experience deleted successfully.'
        );
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
