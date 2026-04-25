<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Shaghalni — Backoffice Dashboard',
                'slug' => 'shaghalni-backoffice-dashboard',
                'description' => 'A modern, feature-rich administration panel for the Shaghalni Job Platform. This panel gives platform administrators and company managers full control over every entity inside the system.',
                'category' => 'Web',
                'tech_stack' => [
                    'PHP 8.5',
                    'Laravel 13',
                    'Tailwind CSS v4',
                    'Alpine.js v3',
                    'MySQL',
                ],
                'images' => [
                    'https://api.mohammedzomlot.online/storage/projects/project_Screenshot_2026-04-25_12-52-41_69ec8f181caf5.png',
                ],
                'live_url' => 'https://dashboard.mohammedzom.online',
                'repo_url' => 'https://github.com/mohammedzom/job-backoffice',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'id' => 3,
                'title' => 'Shaghalni — Job Platform',
                'slug' => 'shaghalni-job-platform',
                'description' => 'A modern, bilingual (Arabic/English) job marketplace connecting employers and job seekers. Features integrated Resume Parsing and AI Analysis, providing a seamless experience for job seekers.',
                'category' => 'Web',
                'tech_stack' => [
                    'PHP 8.5',
                    'Laravel 13',
                    'Tailwind CSS v4',
                    'Alpine.js v3',
                    'MySQL',
                ],
                'images' => [
                    'https://api.mohammedzomlot.online/storage/projects/project_screencapture-mohammedzom-online-2026-04-25-12_55_22_69ec90431b945.png',
                    'https://api.mohammedzomlot.online/storage/projects/project_screencapture-mohammedzom-online-resumes-2026-04-25-12_57_23_69ec90431be4d.png',
                    'https://api.mohammedzomlot.online/storage/projects/project_screencapture-mohammedzom-online-job-applications-2026-04-25-12_57_08_69ec90431c0c5.png',
                    'https://api.mohammedzomlot.online/storage/projects/project_Screenshot_2026-04-25_12-56-57_69ec90431c33f.png',
                ],
                'live_url' => 'https://mohammedzom.online',
                'repo_url' => 'https://github.com/mohammedzom/job-app',
                'is_featured' => false,
                'sort_order' => 2,
            ],
            [
                'id' => 4,
                'title' => 'Portfolio CMS',
                'slug' => 'portfolio-cms',
                'description' => 'A robust backend content management system designed to manage personal portfolios, including dynamic projects, skills, services, and site settings.',
                'category' => 'Web',
                'tech_stack' => [
                    'PHP',
                    'Laravel',
                    'MySQL',
                ],
                'images' => [],
                'live_url' => null,
                'repo_url' => 'https://github.com/mohammedzom/Portfolio-CMS',
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'id' => 5,
                'title' => 'HR System API',
                'slug' => 'hr-system-api',
                'description' => 'An advanced HR system built for the future. Manage attendance, payroll, leaves, and assets with a powerful RESTful API and sleek interface. Features enterprise-grade security with Laravel Sanctum.',
                'category' => 'Web',
                'tech_stack' => [
                    'PHP',
                    'Laravel 12',
                    'REST API',
                    'MySQL',
                ],
                'images' => [
                    'https://api.mohammedzomlot.online/storage/projects/project_screencapture-api-mohammedzom-online-2026-04-25-13_01_59_69ec9314e57d8.png',
                    'https://api.mohammedzomlot.online/storage/projects/project_Screenshot_2026-04-25_13-01-35_69ec9314e5c67.png',
                ],
                'live_url' => 'https://api.mohammedzom.online/',
                'repo_url' => 'https://github.com/mohammedzom/HR-System-API',
                'is_featured' => false,
                'sort_order' => 4,
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(
                ['slug' => $project['slug']],
                [
                    'title' => $project['title'],
                    'description' => $project['description'],
                    'category' => $project['category'],
                    'tech_stack' => $project['tech_stack'],
                    'images' => $project['images'],
                    'live_url' => $project['live_url'],
                    'repo_url' => $project['repo_url'],
                    'is_featured' => $project['is_featured'],
                    'sort_order' => $project['sort_order'],
                ]
            );
        }
    }
}
