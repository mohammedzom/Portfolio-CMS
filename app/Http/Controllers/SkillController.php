<?php

namespace App\Http\Controllers;

use App\Http\Requests\Skills\StoreSkillRequest;
use App\Http\Requests\Skills\UpdateSkillRequest;
use App\Models\Skill;

class SkillController extends Controller
{
    public function index()
    {
        $query = Skill::query();

        if (request()->has('archived') && request()->archived) {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        if (request()->filled('category')) {
            $query->where('category', request()->category);
        }
        if (request()->filled('search')) {
            $query->where('name', 'like', '%'.request()->search.'%');
        }

        $skills = $query->latest()->paginate(10);

        return view('admin.skills.index', compact('skills'));
    }

    public function create()
    {
        return view('admin.skills.create');
    }

    public function store(StoreSkillRequest $request)
    {
        Skill::create($request->validated());

        return redirect()->route('admin.skills.index')->with('success', 'Skill created successfully');
    }

    public function show(Skill $skill)
    {
        return view('admin.skills.show', compact('skill'));
    }

    public function edit(Skill $skill)
    {
        return view('admin.skills.edit', compact('skill'));
    }

    public function update(UpdateSkillRequest $request, Skill $skill)
    {
        $skill->update($request->validated());

        return redirect()->route('admin.skills.index')->with('success', 'Skill updated successfully');
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();

        return redirect()->route('admin.skills.index')->with('success', 'Skill deleted successfully');
    }

    public function restore(Skill $skill)
    {
        $skill->restore();

        return redirect()->route('admin.skills.index')->with('success', 'Skill restored successfully');
    }
}
