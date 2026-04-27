<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\SkillCategory\StoreSkillCategoryRequest;
use App\Http\Requests\SkillCategory\UpdateSkillCategoryRequest;
use App\Http\Resources\SkillCategoryResource;
use App\Models\SkillCategory;
use Illuminate\Http\Request;

class SkillCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = SkillCategory::query();
        if ($request->filled('archived') && $request->archived) {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }
        $skillCategories = $query->with('skills')->get();

        return $this->successResponse(SkillCategoryResource::collection($skillCategories), 'Skill categories fetched successfully');
    }

    public function show($id)
    {
        $category = SkillCategory::with('skills')->findOrFail($id);

        return $this->successResponse(new SkillCategoryResource($category), 'Skill category fetched successfully');
    }

    public function store(StoreSkillCategoryRequest $request)
    {
        $category = SkillCategory::create($request->validated());

        return $this->successResponse(new SkillCategoryResource($category), 'Skill category created successfully');
    }

    public function update(UpdateSkillCategoryRequest $request, string $id)
    {

        $category = SkillCategory::findOrFail($id);
        $category->update($request->validated());

        return $this->successResponse(new SkillCategoryResource($category), 'Skill category updated successfully');
    }

    public function destroy(string $id)
    {
        $category = SkillCategory::findOrFail($id);
        if ($category->skills()->exists()) {
            $category->skills()->delete();
        }
        $category->delete();

        return $this->successResponse([], 'Skill category archived successfully');
    }

    public function restore(string $id)
    {
        $category = SkillCategory::onlyTrashed()->findOrFail($id);
        $deletedAt = $category->deleted_at;
        $category->restore();
        $category->skills()->onlyTrashed()
            ->whereBetween('deleted_at', [
                $deletedAt->copy()->subSeconds(5),
                $deletedAt->copy()->addSeconds(5),
            ])->restore();

        return $this->successResponse(new SkillCategoryResource($category), 'Skill category restored successfully\n Note: Skills inside this category have been restored as well\n Note: Only skills that have been archived in the archive skill category have been restored.');
    }

    public function forceDelete(string $id)
    {
        $category = SkillCategory::withTrashed()->findOrFail($id);
        $category->forceDelete();

        return $this->successResponse([], 'Skill category deleted successfully. \n Note: All skills inside this category have been deleted.');
    }
}
