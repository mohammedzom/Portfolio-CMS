<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\SkillCategory\StoreSkillCategoryRequest;
use App\Http\Requests\SkillCategory\UpdateSkillCategoryRequest;
use App\Http\Resources\SkillCategoryResource;
use App\Models\SkillCategory;
use App\Traits\ManageSoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SkillCategoryController extends Controller
{
    use ManageSoftDeletes;

    protected $modelClass = SkillCategory::class;

    protected $resourceClass = SkillCategoryResource::class;

    public function index(Request $request): JsonResponse
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

    public function show(string $id): JsonResponse
    {
        $category = SkillCategory::with('skills')->findOrFail($id);

        return $this->successResponse(new SkillCategoryResource($category), 'Skill category fetched successfully');
    }

    public function store(StoreSkillCategoryRequest $request): JsonResponse
    {
        $category = SkillCategory::create($request->validated());
        Cache::forget('portfolio_all');

        return $this->successResponse(new SkillCategoryResource($category), 'Skill category created successfully');
    }

    public function update(UpdateSkillCategoryRequest $request, string $id): JsonResponse
    {

        $category = SkillCategory::findOrFail($id);
        $category->update($request->validated());
        Cache::forget('portfolio_all');

        return $this->successResponse(new SkillCategoryResource($category), 'Skill category updated successfully');
    }

    public function destroy(string $id): JsonResponse
    {
        $category = SkillCategory::findOrFail($id);
        if ($category->skills()->exists()) {
            $category->skills()->delete();
        }
        $category->delete();
        Cache::forget('portfolio_all');

        return $this->successResponse([], 'Skill category archived successfully');
    }

    public function restore(string $id): JsonResponse
    {
        $category = SkillCategory::onlyTrashed()->findOrFail($id);
        $deletedAt = $category->deleted_at;
        $category->restore();
        $category->skills()->onlyTrashed()
            ->whereBetween('deleted_at', [
                $deletedAt->copy()->subSeconds(5),
                $deletedAt->copy()->addSeconds(5),
            ])->restore();

        Cache::forget('portfolio_all');

        return $this->successResponse(new SkillCategoryResource($category), "Skill category restored successfully\n Note: Skills inside this category have been restored as well\n Note: Only skills that have been archived in the archive skill category have been restored.");
    }

    protected function afterForceDelete(): void
    {
        Cache::forget('portfolio_all');
    }
}
