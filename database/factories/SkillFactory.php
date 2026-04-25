<?php

namespace Database\Factories;

use App\Models\Skill;
use App\Models\SkillCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'skill_category_id' => SkillCategory::factory(),
            'name' => fake()->unique()->randomElement([
                'PHP', 'Laravel', 'MySQL', 'RESTful APIs', 'Python',
                'JavaScript', 'React', 'Vue.js', 'Tailwind CSS', 'Git',
                'Docker', 'Linux', 'C++', 'Java', 'Flutter', 'Dart',
            ]),
            'icon' => 'https://www.svgrepo.com/show/489281/api.svg',
            'proficiency' => fake()->numberBetween(60, 99),
        ];
    }
}
