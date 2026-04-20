<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 8) as $i) {
            Message::create([
                'name' => fake()->name(),
                'email' => fake()->email(),
                'subject' => fake()->sentence(),
                'body' => fake()->paragraph(3),
                'read_at' => now(),
            ]);
        }

        foreach (range(1, 5) as $i) {
            Message::create([
                'name' => fake()->name(),
                'email' => fake()->email(),
                'subject' => fake()->sentence(),
                'body' => fake()->paragraph(3),
            ]);
        }
    }
}
