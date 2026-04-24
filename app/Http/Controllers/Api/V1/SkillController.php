<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Skills\StoreSkillRequest;
use App\Http\Requests\Skills\UpdateSkillRequest;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SkillController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Skill::query();
        if ($request->has('archived') && $request->archived) {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $skills = $query->orderBy('proficiency', 'desc')->get();

        return $this->successResponse(
            SkillResource::collection($skills),
            'Skills retreived successfully.'
        );
    }

    public function store(StoreSkillRequest $request): JsonResponse
    {
        $skill = Skill::create($request->validated());
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new SkillResource($skill),
            'Skill created successfully.',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $skill = Skill::withoutTrashed()->findOrFail($id);

        return $this->successResponse(
            new SkillResource($skill),
            'Skill fetched successfully.'
        );
    }

    public function update(UpdateSkillRequest $request, string $id): JsonResponse
    {
        $skill = Skill::withoutTrashed()->findOrFail($id);
        $skill->update($request->validated());

        Cache::forget('portfolio_all');

        return $this->successResponse(
            new SkillResource($skill),
            'Skill updated successfully.'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $skill = Skill::withoutTrashed()->findOrFail($id);
        $skill->delete();
        Cache::forget('portfolio_all');

        return $this->successResponse(
            [],
            'Skill Archived successfully.'
        );
    }

    public function restore(string $id): JsonResponse
    {
        $skill = Skill::onlyTrashed()->findOrFail($id);
        $skill->restore();
        Cache::forget('portfolio_all');

        return $this->successResponse(
            new SkillResource($skill),
            'Skill restored successfully.'
        );
    }

    public function forceDelete(string $id): JsonResponse
    {
        $skill = Skill::withTrashed()->findOrFail($id);
        $skill->forceDelete();
        Cache::forget('portfolio_all');

        return $this->successResponse(
            [],
            'Skill deleted successfully.'
        );
    }
}
