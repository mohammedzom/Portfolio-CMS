<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            [
                'title' => 'AWS Certified Developer – Associate',
                'issuer' => 'Amazon Web Services',
                'date' => '2024-03-15',
                'url' => 'https://aws.amazon.com/certification/',
                'description' => 'Validates technical expertise in developing and maintaining applications built on AWS.',
                'image' => null,
            ],
            [
                'title' => 'Meta Front-End Developer Certificate',
                'issuer' => 'Meta (Coursera)',
                'date' => '2023-09-01',
                'url' => 'https://www.coursera.org/professional-certificates/meta-front-end-developer',
                'description' => 'Professional certification covering React, responsive design, and modern front-end development workflows.',
                'image' => null,
            ],
            [
                'title' => 'JavaScript Algorithms and Data Structures',
                'issuer' => 'freeCodeCamp',
                'date' => '2022-06-20',
                'url' => 'https://www.freecodecamp.org/certification/',
                'description' => 'Comprehensive certification in JavaScript fundamentals, ES6+, algorithms, and data structures.',
                'image' => null,
            ],
            [
                'title' => 'Google UX Design Certificate',
                'issuer' => 'Google (Coursera)',
                'date' => '2023-01-10',
                'url' => 'https://grow.google/certificates/ux-design/',
                'description' => 'Covers the full UX design process: empathize, define, ideate, prototype, and test.',
                'image' => null,
            ],
            [
                'title' => 'Best Web Application – Regional Hackathon',
                'issuer' => 'TechFest Arabia',
                'date' => '2023-11-05',
                'url' => null,
                'description' => 'First place award for developing an innovative real-time collaboration tool during a 48-hour hackathon.',
                'image' => null,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::updateOrCreate(
                ['title' => $achievement['title'], 'issuer' => $achievement['issuer']],
                $achievement
            );
        }
    }
}
