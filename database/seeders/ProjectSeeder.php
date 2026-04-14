<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            ['Modern Website',       'Web',    'React + Tailwind',   'A sleek corporate website with smooth animations and lightning-fast performance.',                   true,  1],
            ['Landing Page',         'Web',    'Next.js + Framer',   'High-converting landing page with A/B testing and integrated analytics.',                            false, 2],
            ['E-commerce Store',     'Web',    'Vue + Laravel',      'Full-stack shop with cart, payment integration via Stripe, and admin panel.',                        true,  3],
            ['Mobile App UI',        'App',    'React Native',       'Cross-platform mobile app with clean interface and offline-first architecture.',                      false, 4],
            ['Analytics Dashboard',  'UI/UX',  'Figma',              'Data visualization dashboard design system with 40+ components ready for handoff.',                  true,  5],
            ['Portfolio Design',     'Design', 'Figma + CSS',        'Custom portfolio template for creatives — fully responsive with dark mode support.',                  false, 6],
        ];

        foreach ($projects as [$title, $category, $tech, $description, $featured, $order]) {
            Project::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => $description,
                'category' => $category,
                'tech_stack' => $tech,
                'is_featured' => $featured,
                'sort_order' => $order,
            ]);
        }
    }
}
