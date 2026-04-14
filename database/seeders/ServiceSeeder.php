<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'Web Development',
                'description' => 'Building fast, responsive, and accessible websites using the latest technologies and best practices.',
                'icon' => 'ri-code-box-line',
                'tags' => ['React', 'Next.js', 'Vue', 'Laravel'],
                'sort_order' => 1,
            ],
            [
                'title' => 'UI/UX Design',
                'description' => 'Creating intuitive and visually stunning user interfaces with a focus on user experience and conversion.',
                'icon' => 'ri-layout-4-line',
                'tags' => ['Figma', 'Prototyping', 'Design Systems', 'Wireframing'],
                'sort_order' => 2,
            ],
            [
                'title' => 'Mobile-First Design',
                'description' => 'Every project is built mobile-first ensuring perfect experience across all devices and screen sizes.',
                'icon' => 'ri-smartphone-line',
                'tags' => ['Responsive', 'PWA', 'Performance', 'Accessibility'],
                'sort_order' => 3,
            ],
            [
                'title' => 'Performance Optimization',
                'description' => 'Analyzing and optimizing web performance for lightning-fast load times and smooth interactions.',
                'icon' => 'ri-speed-line',
                'tags' => ['Core Web Vitals', 'SEO', 'Caching', 'Compression'],
                'sort_order' => 4,
            ],
            [
                'title' => 'Open Source',
                'description' => 'Contributing to the community through open source projects and sharing knowledge with other developers.',
                'icon' => 'ri-github-line',
                'tags' => ['npm packages', 'GitHub', 'Documentation', 'Tutorials'],
                'sort_order' => 5,
            ],
            [
                'title' => 'Ongoing Support',
                'description' => 'Providing continuous maintenance, updates, and support to keep your digital products running smoothly.',
                'icon' => 'ri-customer-service-2-line',
                'tags' => ['Monitoring', 'Updates', 'Hotfixes', 'Consulting'],
                'sort_order' => 6,
            ],
        ];

        foreach ($services as $data) {
            Service::create($data);
        }
    }
}
