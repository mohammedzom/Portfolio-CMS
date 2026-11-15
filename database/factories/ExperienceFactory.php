<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startYear = fake()->numberBetween(2015, 2022);

        return [
            'job_title' => fake()->jobTitle(),
            'company' => fake()->company(),
            'description' => fake()->sentence(),
            'start_date' => $startYear,
            'end_date' => $startYear + 1,
            'is_current' => false,
        ];
    }
}
