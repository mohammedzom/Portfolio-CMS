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

        return response()->json([
            'success' => true,
            'message' => 'Experiences retrieved successfully',
            'data' => ExperienceResource::collection($experiences),
        ]);
    }

    public function store(StoreExperienceRequest $request)
    {
        $experience = Experience::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Experience created successfully.',
            'data' => new ExperienceResource($experience),
        ]);
    }

    public function show(string $id)
    {
        $experience = Experience::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Experience retrieved successfully.',
            'data' => new ExperienceResource($experience),
        ]);
    }

    public function update(UpdateExperienceRequest $request, string $id)
    {
        $experience = Experience::withoutTrashed()->findOrFail($id);
        $experience->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Experience updated successfully.',
            'data' => new ExperienceResource($experience),
        ]);
    }

    public function destroy(string $id)
    {
        $experience = Experience::withoutTrashed()->findOrFail($id);
        $experience->delete();

        return response()->json([
            'success' => true,
            'message' => 'Experience Archived successfully.',
            'data' => [],
        ]);
    }

    public function restore(string $id)
    {
        $experience = Experience::onlyTrashed()->findOrFail($id);
        $experience->restore();

        return response()->json([
            'success' => true,
            'message' => 'Experience restored successfully.',
            'data' => new ExperienceResource($experience),
        ]);
    }

    public function forceDelete(string $id)
    {
        $experience = Experience::onlyTrashed()->findOrFail($id);
        $experience->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Experience deleted successfully.',
            'data' => [],
        ]);
    }
}
