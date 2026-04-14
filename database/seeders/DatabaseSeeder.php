<?php

namespace Database\Seeders;

use App\Models\SiteSettings;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@patrick.dev'],
            [
                'name' => 'Patrick Moz',
                'password' => Hash::make('password'),
            ]
        );

        // Portfolio settings (single row)
        SiteSettings::firstOrCreate(
            ['id' => 1],
            [
                'full_name' => 'Patrick Moz',
                'tagline' => 'Front-end Developer & UI Designer',
                'bio' => "I'm a passionate front-end developer with 5+ years of experience creating stunning web interfaces. I specialize in turning complex ideas into clean, intuitive, and high-performance digital experiences.",
                'email' => 'patrick@dev.io',
                'phone' => '+1 (555) 000-0000',
                'location' => 'Casablanca, Morocco',
                'github_url' => 'https://github.com/',
                'linkedin_url' => 'https://linkedin.com/',
                'twitter_url' => 'https://twitter.com/',
                'dribbble_url' => 'https://dribbble.com/',
                'years_experience' => 5,
                'projects_count' => 20,
                'clients_count' => 15,
                'available_for_freelance' => true,
            ]
        );

        $this->call([
            SkillSeeder::class,
            ServiceSeeder::class,
            ProjectSeeder::class,
            MessageSeeder::class,
        ]);
    }
}
