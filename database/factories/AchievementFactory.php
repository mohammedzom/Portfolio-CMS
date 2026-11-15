<?php

namespace Database\Factories;

use App\Models\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Achievement>
 */
class AchievementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'issuer' => fake()->company(),
            'date' => fake()->date(),
            'url' => fake()->url(),
            'description' => fake()->sentence(),
            'certificate_url' => null,
        ];
    }
}
