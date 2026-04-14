<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->bs(),
            'description' => fake()->sentence(15),
            'icon' => fake()->randomElement(['ri-code-box-line', 'ri-layout-4-line', 'ri-smartphone-line', 'ri-speed-line', 'ri-github-line']),
            'tags' => fake()->randomElements(['React', 'Laravel', 'Vue', 'Figma', 'Tailwind', 'PWA', 'SEO', 'API'], 4),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
