<?php

namespace App\Http\Controllers;

use App\Models\Skill;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::all();

        return view('admin.skills.index', compact('skills'));
    }
}
