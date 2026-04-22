<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        // Technical skills (with progress bars)
        $technical = [
            ['HTML & CSS',  95],
            ['JavaScript / TypeScript', 90],
            ['React / Vue.js', 85],
            ['Tailwind CSS', 92],
            ['Node.js / PHP', 75],
            ['Figma / UI Design', 80],
        ];

        foreach ($technical as [$name, $proficiency]) {
            Skill::create([
                'name' => $name,
                'icon' => 'https://www.svgrepo.com/show/489281/api.svg',
                'proficiency' => $proficiency,
                'type' => 'technical',
            ]);
        }

        // Tools (icon grid)
        $tools = [
            'HTML5',
            'CSS3',
            'JavaScript',
            'React',
            'Vue',
            'Git',
            'GitHub',
            'Figma',
            'MySQL',
            'Laravel',
            'Tailwind',
            'CLI',
        ];

        foreach ($tools as $tool) {
            Skill::create([
                'name' => $tool,
                'icon' => 'https://www.svgrepo.com/show/489281/api.svg',
                'proficiency' => 0,
                'type' => 'tool',
            ]);
        }
    }
}
