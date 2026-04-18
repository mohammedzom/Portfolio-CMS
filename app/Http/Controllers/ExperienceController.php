<?php

namespace App\Http\Controllers;

use App\Http\Requests\Experiences\StoreExperienceRequest;
use App\Http\Requests\Experiences\UpdateExperienceRequest;
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

        return view('admin.experiences.index', compact('experiences'));
    }

    public function create()
    {
        return view('admin.experiences.create');
    }

    public function store(StoreExperienceRequest $request)
    {
        Experience::create($request->validated());

        return redirect()->route('admin.experience.index')->with('success', 'Experience created successfully.');
    }

    public function show(Experience $experience)
    {
        return view('admin.experiences.show', compact('experience'));
    }

    public function update(UpdateExperienceRequest $request, Experience $experience)
    {
        $experience->update($request->validated());

        return redirect()->route('admin.experience.index')->with('success', 'Experience updated successfully.');
    }

    public function destroy(Experience $experience)
    {
        $experience->delete();

        return redirect()->route('admin.experience.index')->with('success', 'Experience deleted successfully.');
    }

    public function restore(Experience $experience)
    {
        $experience->restore();

        return redirect()->route('admin.experience.index')->with('success', 'Experience restored successfully.');
    }
}
