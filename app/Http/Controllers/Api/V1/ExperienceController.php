<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Experiences\StoreExperienceRequest;
use App\Http\Requests\Experiences\UpdateExperienceRequest;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use Illuminate\Http\Request;

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

        $experiences = $query->orderBy('start_date', 'desc')->get();

        return $this->successResponse(
            ExperienceResource::collection($experiences),
            'Experiences retrieved successfully'
        );
    }

    public function store(StoreExperienceRequest $request)
    {
        $experience = Experience::create($request->validated());

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
        $experience->update($request->validated());

        return $this->successResponse(
            new ExperienceResource($experience),
            'Experience updated successfully.'
        );
    }

    public function destroy(string $id)
    {
        $experience = Experience::withoutTrashed()->findOrFail($id);
        $experience->delete();

        return $this->successResponse(
            [],
            'Experience Archived successfully.',
            204
        );
    }

    public function restore(string $id)
    {
        $experience = Experience::onlyTrashed()->findOrFail($id);
        $experience->restore();

        return $this->successResponse(
            new ExperienceResource($experience),
            'Experience restored successfully.'
        );
    }

    public function forceDelete(string $id)
    {
        $experience = Experience::findOrFail($id);
        $experience->forceDelete();

        return $this->successResponse(
            [],
            'Experience deleted successfully.',
            204
        );
    }
}
