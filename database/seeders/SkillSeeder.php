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
            ['HTML & CSS',              'ri-html5-line',        '#e34f26', 95],
            ['JavaScript / TypeScript', 'ri-javascript-line',   '#f7df1e', 90],
            ['React / Vue.js',          'ri-reactjs-line',      '#61dafb', 85],
            ['Tailwind CSS',            'ri-layout-4-line',     '#06b6d4', 92],
            ['Node.js / PHP',           'ri-server-line',       '#68a063', 75],
            ['Figma / UI Design',       'ri-pen-nib-line',      '#f24e1e', 80],
        ];

        foreach ($technical as [$name, $icon, $color, $proficiency]) {
            Skill::create([
                'name' => $name,
                'icon' => $icon,
                'color' => $color,
                'proficiency' => $proficiency,
                'type' => 'technical',
            ]);
        }

        // Tools (icon grid)
        $tools = [
            ['HTML5',      'ri-html5-fill',         '#e34f26'],
            ['CSS3',       'ri-css3-fill',          '#1572b6'],
            ['JavaScript', 'ri-javascript-fill',    '#f7df1e'],
            ['React',      'ri-reactjs-fill',       '#61dafb'],
            ['Vue',        'ri-vuejs-fill',         '#42b883'],
            ['Git',        'ri-git-branch-line',    '#f05032'],
            ['GitHub',     'ri-github-fill',        '#ffffff'],
            ['Figma',      'ri-figma-line',         '#f24e1e'],
            ['MySQL',      'ri-database-2-line',    '#4479a1'],
            ['Laravel',    'ri-server-2-line',      '#ff2d20'],
            ['Tailwind',   'ri-layout-masonry-line', '#06b6d4'],
            ['CLI',        'ri-terminal-box-line',  '#00d1b2'],
        ];

        foreach ($tools as [$name, $icon, $color]) {
            Skill::create([
                'name' => $name,
                'icon' => $icon,
                'color' => $color,
                'proficiency' => 0,
                'type' => 'tool',
            ]);
        }
    }
}
