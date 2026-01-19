<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PreferredPosition;

class PreferredPositionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Software Engineer',
            'Backend Developer',
            'Frontend Developer',
            'Full Stack Developer',
            'Mobile Developer',
            'DevOps Engineer',
            'System Administrator',
            'Database Administrator',
            'QA Engineer',
            'Automation Engineer',
            'UI/UX Designer',
            'Product Manager',
            'Project Manager',
            'Business Analyst',
            'Data Analyst',
            'Data Scientist',
            'Machine Learning Engineer',
            'AI Engineer',
            'Cloud Engineer',
            'Cybersecurity Analyst',
            'Network Engineer',
            'IT Support Specialist',
            'Technical Writer',
            'Game Developer',
            'Embedded Systems Engineer',
        ];

        $levels = [
            'Intern',
            'Junior',
            'Mid-Level',
            'Senior',
            'Lead',
            'Principal',
            'Staff',
            'Architect',
        ];

        $technologies = [
            'PHP',
            'Laravel',
            'Java',
            'Spring',
            'Python',
            'Django',
            'Flask',
            'JavaScript',
            'TypeScript',
            'React',
            'Vue',
            'Angular',
            'Node.js',
            'Next.js',
            'Nuxt',
            'Go',
            'Rust',
            'C#',
            '.NET',
            'Kotlin',
            'Swift',
            'Flutter',
            'React Native',
            'AWS',
            'Azure',
            'GCP',
            'Docker',
            'Kubernetes',
            'MySQL',
            'PostgreSQL',
            'MongoDB',
        ];

        $positions = [];

        foreach ($roles as $role) {
            foreach ($levels as $level) {
                foreach ($technologies as $tech) {
                    $positions[] = [
                        'name' => "{$level} {$role} ({$tech})",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Stop once we reach 3000+
                    if (count($positions) >= 3000) {
                        break 3;
                    }
                }
            }
        }

        // Insert in chunks (better performance)
        collect($positions)->chunk(500)->each(function ($chunk) {
            PreferredPosition::insert($chunk->toArray());
        });
    }
}
