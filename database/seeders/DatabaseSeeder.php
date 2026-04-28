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
        SiteSettings::updateOrCreate(
            ['id' => 1],
            [
                'first_name' => 'Mohammed',
                'last_name' => 'Zomlot',
                'tagline' => 'Software Engineer',
                'bio' => 'Specialized in building robust, scalable server-side architectures using PHP & Laravel. Passionate about clean code, RESTful APIs, and database optimization.',
                'about_me' => 'Resilient Software Engineering student with a strong foundation in algorithmic problem-solving. I began my journey as a passionate Mobile Developer (Flutter), building several feature-rich applications. However, due to the war in Gaza and the resulting hardware limitations of my available device, I demonstrated adaptability by pivoting to Backend Development. I now specialize in PHP, Laravel, and RESTful APIs, leveraging my engineering mindset to build robust server-side solutions while continuing to excel in regional programming contests.',
                'avatar' => 'avatars/avatar.jpg',
                'cv_file' => 'cv/Mohammed_Zomlot-CV.pdf',
                'email' => 'mohammedzomlot2@gmail.com',
                'phone' => '+970593628153',
                'location' => 'Gaza Strip, Palestine',
                'social_links' => [
                    ['name' => 'github', 'url' => 'https://github.com/mohammedzom'],
                    ['name' => 'linkedin', 'url' => 'https://www.linkedin.com/in/mohammedzom/'],
                    ['name' => 'Telegram', 'url' => 'https://t.me/mohammedzom'],
                ],
                'years_experience' => 1,
                'projects_count' => 4,
                'clients_count' => 3,
                'available_for_freelance' => true,
                'url_prefix' => 'Mohammedzomlot',
                'url_suffix' => 'dev',
                'languages' => [
                    ['name' => 'Arabic', 'level' => 'Native'],
                    ['name' => 'English', 'level' => 'Intermediate'],
                ],
            ]
        );

        $this->call([
            SkillSeeder::class,
            ServiceSeeder::class,
            ProjectSeeder::class,
            MessageSeeder::class,
            ExperienceSeeder::class,
            AchievementSeeder::class,
            EducationSeeder::class,
            VisitSeeder::class,
        ]);
    }
}
