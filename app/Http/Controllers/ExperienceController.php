<?php

namespace App\Http\Controllers;

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

        $experiences = $query->orderBy('start_date', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Experiences retrieved successfully',
            'data' => ExperienceResource::collection($experiences),
            'meta' => [
                'current_page' => $experiences->currentPage(),
                'last_page' => $experiences->lastPage(),
                'total' => $experiences->total(),
                'per_page' => $experiences->perPage(),
            ],
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

    public function show(Experience $experience)
    {
        return response()->json([
            'success' => true,
            'message' => 'Experience retrieved successfully.',
            'data' => new ExperienceResource($experience),
        ]);
    }

    public function update(UpdateExperienceRequest $request, Experience $experience)
    {
        $experience->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Experience updated successfully.',
            'data' => new ExperienceResource($experience),
        ]);
    }

    public function destroy(Experience $experience)
    {
        $experience->delete();

        return response()->json([
            'success' => true,
            'message' => 'Experience deleted successfully.',
            'data' => [],
        ]);
    }

    public function restore(string $id)
    {
        $experience = Experience::withTrashed()->findOrFail($id);
        if (! $experience) {
            return response()->json([
                'success' => false,
                'message' => 'Experience not found.',
                'data' => [],
            ]);
        }
        $experience->restore();

        return response()->json([
            'success' => true,
            'message' => 'Experience restored successfully.',
            'data' => new ExperienceResource($experience),
        ]);
    }
}
