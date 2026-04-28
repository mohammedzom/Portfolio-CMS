<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Skills\DestroySkillAction;
use App\Actions\Skills\ForceDeleteSkillAction;
use App\Actions\Skills\RestoreSkillAction;
use App\Actions\Skills\StoreSkillAction;
use App\Actions\Skills\UpdateSkillAction;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Skills\StoreSkillRequest;
use App\Http\Requests\Skills\UpdateSkillRequest;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Skill::query();

        if ($request->boolean('archived')) {
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
        $skill = StoreSkillAction::run($request->validated());

        return $this->successResponse(
            new SkillResource($skill),
            'Skill created successfully.',
            201
        );
    }

    public function show(Skill $skill): JsonResponse
    {
        $skill->load('category');

        return $this->successResponse(
            new SkillResource($skill),
            'Skill fetched successfully.'
        );
    }

    public function update(UpdateSkillRequest $request, Skill $skill): JsonResponse
    {
        $skill = UpdateSkillAction::run($skill, $request->validated());

        return $this->successResponse(
            new SkillResource($skill),
            'Skill updated successfully.'
        );
    }

    public function destroy(Skill $skill): JsonResponse
    {
        DestroySkillAction::run($skill);

        return $this->successResponse(
            [],
            'Skill Archived successfully.'
        );
    }

    public function restore(Request $request, Skill $skill): JsonResponse
    {
        $validated = $request->validate([
            'new_category_id' => 'nullable|exists:skill_categories,id,deleted_at,NULL',
        ]);

        $skill = RestoreSkillAction::run(
            $skill,
            isset($validated['new_category_id']) ? (int) $validated['new_category_id'] : null
        );

        return $this->successResponse(
            new SkillResource($skill),
            'Skill restored successfully.'
        );
    }

    public function forceDelete(Skill $skill): JsonResponse
    {
        ForceDeleteSkillAction::run($skill);

        return $this->successResponse(
            [],
            'Skill deleted permanently.'
        );
    }
}
