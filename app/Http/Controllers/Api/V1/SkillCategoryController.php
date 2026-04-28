<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\SkillCategory\DestroySkillCategoryAction;
use App\Actions\SkillCategory\ForceDeleteSkillCategoryAction;
use App\Actions\SkillCategory\RestoreSkillCategoryAction;
use App\Actions\SkillCategory\StoreSkillCategoryAction;
use App\Actions\SkillCategory\UpdateSkillCategoryAction;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\SkillCategory\StoreSkillCategoryRequest;
use App\Http\Requests\SkillCategory\UpdateSkillCategoryRequest;
use App\Http\Resources\SkillCategoryResource;
use App\Models\SkillCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SkillCategory::query();

        if ($request->boolean('archived')) {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }
        $skillCategories = $query->with('skills')->get();

        return $this->successResponse(SkillCategoryResource::collection($skillCategories), 'Skill categories fetched successfully');
    }

    public function show(SkillCategory $skillCategory): JsonResponse
    {
        $skillCategory->load('skills');

        return $this->successResponse(new SkillCategoryResource($skillCategory), 'Skill category fetched successfully');
    }

    public function store(StoreSkillCategoryRequest $request): JsonResponse
    {
        $category = StoreSkillCategoryAction::run($request->validated());

        return $this->successResponse(new SkillCategoryResource($category), 'Skill category created successfully');
    }

    public function update(UpdateSkillCategoryRequest $request, SkillCategory $skillCategory): JsonResponse
    {
        $category = UpdateSkillCategoryAction::run($skillCategory, $request->validated());

        return $this->successResponse(new SkillCategoryResource($category), 'Skill category updated successfully');
    }

    public function destroy(SkillCategory $skillCategory): JsonResponse
    {
        DestroySkillCategoryAction::run($skillCategory);

        return $this->successResponse([], 'Skill category archived successfully');
    }

    public function restore(SkillCategory $skillCategory): JsonResponse
    {
        $category = RestoreSkillCategoryAction::run($skillCategory);

        return $this->successResponse(new SkillCategoryResource($category), "Skill category restored successfully\n Note: Skills inside this category have been restored as well\n Note: Only skills that have been archived in the archive skill category have been restored.");
    }

    public function forceDelete(SkillCategory $skillCategory): JsonResponse
    {
        ForceDeleteSkillCategoryAction::run($skillCategory);

        return $this->successResponse(
            [],
            'SkillCategory deleted permanently.'
        );
    }
}
