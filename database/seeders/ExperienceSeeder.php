<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Experience::create([
            'job_title' => 'Senior Front-end Developer',
            'company' => 'Acme Studio',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'start_date' => '2023',
            'end_date' => null,
        ]);

        Experience::create([
            'job_title' => 'Front-end Developer',
            'company' => 'WebCraft Co.',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'start_date' => '2021',
            'end_date' => '2023',
        ]);

        Experience::create([
            'job_title' => 'Junior Developer',
            'company' => 'Freelance',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'start_date' => '2019',
            'end_date' => '2021',
        ]);
    }
}
