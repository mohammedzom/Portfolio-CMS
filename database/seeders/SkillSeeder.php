<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skillsByCategory = [
            'Backend Development' => [
                ['PHP', 95, 'https://cdn-icons-png.flaticon.com/512/5968/5968332.png'],
                ['Laravel', 90, 'https://icon.icepanel.io/Technology/svg/Laravel.svg'],
                ['RESTful APIs', 90, 'https://www.svgrepo.com/show/489281/api.svg'],
                ['MySQL', 90, 'https://icon.icepanel.io/Technology/svg/MySQL.svg'],
                ['Database Design', 85, 'https://cdn-icons-png.flaticon.com/512/2758/2758751.png'],
            ],
            'Mobile Development' => [
                ['Flutter', 70, 'https://icon.icepanel.io/Technology/svg/Flutter.svg'],
                ['Dart', 70, 'https://icon.icepanel.io/Technology/svg/Dart.svg'],
            ],
            'Programming & Tools' => [
                ['Git & GitHub', 90, 'https://icon.icepanel.io/Technology/png-shadow-512/GitHub.png'],
                ['Linux/Unix', 95, 'https://icon.icepanel.io/Technology/png-shadow-512/Linux.png'],
                ['Postman', 90, 'https://icon.icepanel.io/Technology/svg/Postman.svg'],
                ['Python', 85, 'https://icon.icepanel.io/Technology/svg/Python.svg'],
                ['C++', 80, 'https://icon.icepanel.io/Technology/svg/C%2B%2B-%28CPlusPlus%29.svg'],
                ['Java', 70, 'https://icon.icepanel.io/Technology/svg/Java.svg'],
            ],
            'Core Concepts' => [
                ['Problem Solving', 80, 'https://api.mohammedzomlot.online/storage/icons/problem_solving.svg'],
                ['System Design', 80, 'https://api.mohammedzomlot.online/storage/icons/system-design.png'],
                ['Algorithms', 80, 'https://icon.icepanel.io/Technology/svg/The-Algorithms.svg'],
                ['DevOps Basics', 90, 'https://api.mohammedzomlot.online/storage/icons/devops.png'],
            ],
        ];

        foreach ($skillsByCategory as $categoryName => $skills) {
            $category = SkillCategory::firstOrCreate(
                ['name' => $categoryName],
                ['slug' => Str::slug($categoryName)]
            );

            foreach ($skills as [$name, $proficiency, $icon]) {
                Skill::updateOrCreate(
                    ['name' => $name],
                    [
                        'skill_category_id' => $category->id,
                        'proficiency' => $proficiency,
                        'icon' => $icon,
                    ]
                );
            }
        }
    }
}
