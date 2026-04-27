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
        if ($request->has('archived') && $request->input('archived') == true) {
            $query->onlyTrashed();
            $query->with(['category' => function ($q) {
                $q->withTrashed();
            }]);
        } else {
            $query->withoutTrashed();
            $query->with('category');
        }

        if ($request->filled('category')) {
            $query->where('skill_category_id', $request->category);
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
        $skill = Skill::withoutTrashed()->with('category')->findOrFail($id);

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

    public function restore(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'new_category_id' => 'nullable|exists:skill_categories,id,deleted_at,NULL',
        ]);

        $skill = Skill::onlyTrashed()->with(['category' => fn ($q) => $q->withTrashed()])->findOrFail($id);

        $isCategoryTrashed = $skill->category && $skill->category->trashed();

        if ($isCategoryTrashed && ! $request->has('new_category_id')) {

            return $this->errorResponse(
                'Category is Archived. Please restore the category first to restore the skill.\n Or send in body request new_category_id value to assign it to an active category.',
                409,
            );
        }

        if ($request->has('new_category_id') && $validated['new_category_id'] !== null) {
            $skill->update([
                'skill_category_id' => $validated['new_category_id'],
            ]);
        }

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
