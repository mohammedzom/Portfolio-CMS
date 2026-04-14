<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        Message::factory()->count(8)->read()->create();
        Message::factory()->count(5)->unread()->create();
    }
}
