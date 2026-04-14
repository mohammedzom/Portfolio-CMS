<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'subject' => fake()->sentence(5),
            'body' => fake()->paragraphs(2, true),
            'is_read' => fake()->boolean(40),
            'read_at' => null,
        ];
    }

    public function unread(): static
    {
        return $this->state(['is_read' => false, 'read_at' => null]);
    }

    public function read(): static
    {
        return $this->state([
            'is_read' => true,
            'read_at' => fake()->dateTimeThisMonth(),
        ]);
    }
}
