<?php

namespace Database\Factories;

use App\Models\Education;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Education>
 */
class EducationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'degree' => $this->faker->word() . ' Degree',
            'institution' => $this->faker->company() . ' University',
            'field_of_study' => $this->faker->jobTitle(),
            'start_year' => $this->faker->numberBetween(2010, 2015),
            'end_year' => $this->faker->numberBetween(2016, 2020),
            'gpa' => $this->faker->randomFloat(2, 2.0, 4.0),
            'description' => $this->faker->paragraph(),
        ];
    }
}
