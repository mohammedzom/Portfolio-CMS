<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->words(3, true);

        return [
            'title' => ucwords($title),
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 999),
            'description' => fake()->sentence(12),
            'category' => json_encode(fake()->randomElements(['Web', 'App', 'Design', 'UI/UX'], 2)),
            'tech_stack' => json_encode(fake()->randomElements(['React + Tailwind', 'Next.js + Framer', 'Vue + Laravel', 'React Native', 'Figma', 'Figma + CSS'], 4)),
            'image' => null,
            'live_url' => fake()->url(),
            'repo_url' => 'https://github.com/'.fake()->userName().'/'.Str::slug($title),
            'is_featured' => fake()->boolean(30),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }

    public function web(): static
    {
        return $this->state(['category' => json_encode(['Web'])]);
    }
}
