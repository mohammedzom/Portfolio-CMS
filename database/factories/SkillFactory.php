<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['HTML & CSS', 'JavaScript', 'TypeScript', 'React', 'Vue.js', 'Tailwind CSS', 'Node.js', 'PHP', 'Laravel', 'Figma', 'Git']),
            'icon' => fake()->randomElement(['ri-html5-fill', 'ri-javascript-fill', 'ri-reactjs-fill', 'ri-vuejs-fill', 'ri-css3-fill']),
            'color' => fake()->hexColor(),
            'proficiency' => fake()->numberBetween(60, 99),
            'type' => fake()->randomElement(['technical', 'tool']),
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function technical(): static
    {
        return $this->state(['type' => 'technical']);
    }

    public function tool(): static
    {
        return $this->state(['type' => 'tool']);
    }
}
