<?php

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    public function run(): void
    {
        $educations = [
            [
                'degree' => 'Bachelor of Science',
                'institution' => 'King Abdulaziz University',
                'field_of_study' => 'Computer Science',
                'start_year' => 2017,
                'end_year' => 2021,
                'gpa' => '3.8',
                'description' => 'Focused on software engineering, algorithms, and web technologies. Graduated with honors.',
            ],
            [
                'degree' => 'Diploma',
                'institution' => 'Orange Digital Center',
                'field_of_study' => 'Full-Stack Web Development',
                'start_year' => 2021,
                'end_year' => 2022,
                'gpa' => null,
                'description' => 'Intensive bootcamp covering modern web technologies including React, Node.js, and cloud deployment.',
            ],
            [
                'degree' => 'Online Specialization',
                'institution' => 'Udemy / Coursera',
                'field_of_study' => 'UI/UX Design & Front-End Engineering',
                'start_year' => 2022,
                'end_year' => null,
                'gpa' => null,
                'description' => 'Ongoing self-directed study in advanced UI/UX patterns, design systems, and modern JavaScript frameworks.',
            ],
        ];

        foreach ($educations as $education) {
            Education::updateOrCreate(
                ['degree' => $education['degree'], 'institution' => $education['institution']],
                $education
            );
        }
    }
}
