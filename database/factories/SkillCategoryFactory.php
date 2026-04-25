<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SkillCategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Backend Development',
            'Frontend Development',
            'Mobile Development',
            'Programming & Tools',
            'Core Concepts',
            'DevOps',
            'Databases',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
