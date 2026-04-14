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
            ['email' => 'admin@mohammedzomlot.dev'],
            [
                'name' => 'Mohammed Zomlot',
                'password' => Hash::make('12345678'),
            ]
        );

        // Portfolio settings (single row)
        SiteSettings::firstOrCreate(
            ['id' => 1],
            [
                'first_name' => 'Mohammed',
                'last_name' => 'Zomlot',
                'tagline' => 'Front-end Developer & UI Designer',
                'bio' => "I'm a passionate front-end developer with 5+ years of experience creating stunning web interfaces. I specialize in turning complex ideas into clean, intuitive, and high-performance digital experiences.",
                'about_me' => "I'm a passionate front-end developer with 5+ years of experience creating stunning web interfaces. I specialize in turning complex ideas into clean, intuitive, and high-performance digital experiences.",
                'email' => 'patrick@dev.io',
                'phone' => '+1 (555) 000-0000',
                'location' => 'Casablanca, Morocco',
                'social_links' => [
                    ['url' => 'https://github.com/', 'icon' => 'ri-github-line', 'name' => 'GitHub'],
                    ['url' => 'https://linkedin.com/', 'icon' => 'ri-linkedin-line', 'name' => 'LinkedIn'],
                    ['url' => 'https://twitter.com/', 'icon' => 'ri-twitter-line', 'name' => 'Twitter'],
                    ['url' => 'https://dribbble.com/', 'icon' => 'ri-dribbble-line', 'name' => 'Dribbble'],
                ],
                'years_experience' => 5,
                'projects_count' => 20,
                'clients_count' => 15,
                'available_for_freelance' => true,
                'url_prefix' => 'Mohammedzomlot',
                'url_suffix' => 'dev',
                'languages' => [
                    'Arabic (Native)',
                    'English',
                ],

            ]
        );

        $this->call([
            SkillSeeder::class,
            ServiceSeeder::class,
            ProjectSeeder::class,
            MessageSeeder::class,
            ExperienceSeeder::class,
        ]);
    }
}
