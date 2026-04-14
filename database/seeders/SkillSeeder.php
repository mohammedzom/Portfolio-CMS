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
            ['HTML & CSS',              'ri-html5-line',        '#e34f26', 95, 1],
            ['JavaScript / TypeScript', 'ri-javascript-line',   '#f7df1e', 90, 2],
            ['React / Vue.js',          'ri-reactjs-line',      '#61dafb', 85, 3],
            ['Tailwind CSS',            'ri-layout-4-line',     '#06b6d4', 92, 4],
            ['Node.js / PHP',           'ri-server-line',       '#68a063', 75, 5],
            ['Figma / UI Design',       'ri-pen-nib-line',      '#f24e1e', 80, 6],
        ];

        foreach ($technical as [$name, $icon, $color, $proficiency, $order]) {
            Skill::create([
                'name' => $name,
                'icon' => $icon,
                'color' => $color,
                'proficiency' => $proficiency,
                'type' => 'technical',
                'sort_order' => $order,
            ]);
        }

        // Tools (icon grid)
        $tools = [
            ['HTML5',      'ri-html5-fill',         '#e34f26', 1],
            ['CSS3',       'ri-css3-fill',          '#1572b6', 2],
            ['JavaScript', 'ri-javascript-fill',    '#f7df1e', 3],
            ['React',      'ri-reactjs-fill',       '#61dafb', 4],
            ['Vue',        'ri-vuejs-fill',         '#42b883', 5],
            ['Git',        'ri-git-branch-line',    '#f05032', 6],
            ['GitHub',     'ri-github-fill',        '#ffffff', 7],
            ['Figma',      'ri-figma-line',         '#f24e1e', 8],
            ['MySQL',      'ri-database-2-line',    '#4479a1', 9],
            ['Laravel',    'ri-server-2-line',      '#ff2d20', 10],
            ['Tailwind',   'ri-layout-masonry-line', '#06b6d4', 11],
            ['CLI',        'ri-terminal-box-line',  '#00d1b2', 12],
        ];

        foreach ($tools as [$name, $icon, $color, $order]) {
            Skill::create([
                'name' => $name,
                'icon' => $icon,
                'color' => $color,
                'proficiency' => 0,
                'type' => 'tool',
                'sort_order' => $order,
            ]);
        }
    }
}
