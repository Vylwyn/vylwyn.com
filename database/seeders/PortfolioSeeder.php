<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Enums\TechnologyCategory;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds the real portfolio content.
 *
 * Uses updateOrCreate keyed on slug so running this repeatedly is safe — it
 * refreshes existing rows instead of duplicating them. That matters once you
 * have real content you don't want to wipe with a migrate:fresh.
 *
 * TODO(Vylwyn): fields marked NEEDS CONFIRMATION are left null on purpose
 * rather than guessed. Fill them in via the admin panel once Filament is up.
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $technologies = $this->seedTechnologies();

        $this->seedProjects($technologies);
        $this->seedExperiences();
    }

    /**
     * @return array<string, Technology> keyed by slug
     */
    private function seedTechnologies(): array
    {
        $definitions = [
            // Backend
            ['PHP 8.4', TechnologyCategory::Backend, 1],
            ['Laravel', TechnologyCategory::Backend, 2],
            ['Livewire', TechnologyCategory::Backend, 3],
            ['MySQL', TechnologyCategory::Backend, 4],
            ['REST APIs', TechnologyCategory::Backend, 5],
            ['Eloquent', TechnologyCategory::Backend, 6],

            // Frontend
            ['Blade', TechnologyCategory::Frontend, 1],
            ['Tailwind CSS', TechnologyCategory::Frontend, 2],
            ['Alpine.js', TechnologyCategory::Frontend, 3],
            ['JavaScript', TechnologyCategory::Frontend, 4],

            // Mobile
            ['Flutter', TechnologyCategory::Mobile, 1],
            ['Dart', TechnologyCategory::Mobile, 2],
            ['SQLite', TechnologyCategory::Mobile, 3],
            ['Firebase', TechnologyCategory::Mobile, 4],

            // Tooling
            ['Git', TechnologyCategory::Tooling, 1],
            ['Pest', TechnologyCategory::Tooling, 2],
            ['Vite', TechnologyCategory::Tooling, 3],
            ['Linux', TechnologyCategory::Tooling, 4],
            ['cPanel', TechnologyCategory::Tooling, 5],

            // Project tags that don't belong in the skills grid.
            ['Intervention Image', TechnologyCategory::Backend, 90, false],
            ['i18n / RTL', TechnologyCategory::Frontend, 90, false],
            ['WebP', TechnologyCategory::Frontend, 91, false],
            ['PDF generation', TechnologyCategory::Backend, 91, false],
            ['Offline sync', TechnologyCategory::Mobile, 90, false],
        ];

        $technologies = [];

        foreach ($definitions as $definition) {
            /**
             * Destructuring can't declare defaults, so the optional fourth
             * element is read separately. Most rows omit it and default to true.
             */
            [$name, $category, $sortOrder] = $definition;
            $showInSkills = $definition[3] ?? true;

            $slug = Str::slug($name);

            $technologies[$slug] = Technology::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'category' => $category,
                    'sort_order' => $sortOrder,
                    'show_in_skills' => $showInSkills,
                ],
            );
        }

        return $technologies;
    }

    /**
     * @param  array<string, Technology>  $technologies
     */
    private function seedProjects(array $technologies): void
    {
        $projects = [
            [
                'title' => 'Coolpex Water Filters',
                'slug' => 'coolpex-water-filters',
                'tagline' => 'Bilingual commercial site built around how customers actually buy.',
                'summary' => 'A bilingual site for an official Coolpex representative in Kuwait. Built around a WhatsApp-first conversion funnel instead of a contact form, because that is how customers here actually buy. Full English and Arabic with RTL handling, a video offer carousel, and asset delivery tuned for mobile data.',
                'status' => ProjectStatus::Live,
                'client' => 'Coolpex Kuwait',
                'year' => null, // NEEDS CONFIRMATION
                'live_url' => 'https://coolpexwaterfilters.com',
                'is_featured' => true,
                'sort_order' => 1,
                'published_at' => now(),
                'stack' => ['laravel', 'blade', 'tailwind-css', 'i18n-rtl', 'webp', 'javascript'],
            ],
            [
                'title' => 'Interactive Wedding & Photo Booth Platform',
                'slug' => 'wedding-photo-booth-platform',
                'tagline' => 'A full-stack Laravel app built from scratch, constrained by shared hosting.',
                'summary' => 'Live countdown, per-guest event details, a multi-invite PDF generation system, and a guest photo booth with server-side image processing behind an admin moderation queue. Shared hosting shaped almost every architectural decision — no queue workers, no Redis, limited memory for image handling.',
                'status' => ProjectStatus::Live,
                'client' => null,
                'year' => null, // NEEDS CONFIRMATION
                'live_url' => 'https://vylwynriyona.com',
                'is_featured' => true,
                'sort_order' => 2,
                'published_at' => now(),
                'stack' => ['laravel', 'mysql', 'intervention-image', 'pdf-generation', 'tailwind-css', 'cpanel'],
            ],
            [
                'title' => 'RoomSphere — Workspace Intelligence',
                'slug' => 'roomsphere',
                'tagline' => 'Meeting rooms, devices and schedules in one system.',
                'summary' => 'Role-based dashboards, real-time iPad room displays, device heartbeat monitoring, booking conflict prevention, and offline-resilient sync between a Flutter client and a Laravel API. Currently being reworked.',
                'status' => ProjectStatus::InProgress,
                'client' => null,
                'year' => null, // NEEDS CONFIRMATION
                'live_url' => null,
                'is_featured' => true,
                'sort_order' => 3,
                'published_at' => now(),
                'stack' => ['laravel', 'rest-apis', 'flutter', 'mysql', 'offline-sync'],
            ],
        ];

        foreach ($projects as $data) {
            $stack = $data['stack'];
            unset($data['stack']);

            $project = Project::updateOrCreate(['slug' => $data['slug']], $data);

            $ids = collect($stack)
                ->map(fn (string $slug): ?int => $technologies[$slug]->id ?? null)
                ->filter()
                ->all();

            /** Replaces the whole set rather than appending, so re-seeding stays clean. */
            $project->technologies()->sync($ids);
        }
    }

    private function seedExperiences(): void
    {
        /**
         * Dates use the first of the month — LinkedIn only records month and year.
         */
        $experiences = [
            [
                'role' => 'Information Technology Team Lead',
                'organisation' => 'Alghanim International',
                'location' => 'Kuwait',
                'started_on' => '2017-07-01',
                'ended_on' => null, // current role
                'summary' => 'Lead a 22-person team across IT support (18) and procurement (4) for a large diversified conglomerate. Day-to-day this is service delivery, escalation, vendor management and approval workflows — the operational side of enterprise IT, at scale. It is also where most of my product instincts come from: I have spent nine years watching where internal tools break down for the people who have to use them.',
                'sort_order' => 1,
            ],
            [
                'role' => 'Telecommunication',
                'organisation' => 'Ali Alghanim & Sons Group',
                'location' => 'Kuwait',
                'started_on' => '2016-06-01',
                'ended_on' => '2017-07-01',
                'summary' => null,  // NEEDS CONFIRMATION
                'sort_order' => 2,
            ],
            [
                'role' => 'Software Engineering Intern',
                'organisation' => 'Davlin Software Pvt. Ltd',
                'location' => 'Mangaluru, Karnataka, India',
                'started_on' => '2015-01-01',
                'ended_on' => '2015-06-01',
                'summary' => null,
                'sort_order' => 3,
            ],
            [
                'role' => 'Monitoring Executive',
                'organisation' => 'MSDI (A Perform Group Company)',
                'location' => 'Mangaluru, Karnataka, India',
                'started_on' => '2011-08-01',
                'ended_on' => '2012-09-01',
                'summary' => null,
                'sort_order' => 4,
            ],
        ];

        foreach ($experiences as $data) {
            Experience::updateOrCreate(
                [
                    'organisation' => $data['organisation'],
                    'started_on' => $data['started_on'],
                ],
                $data,
            );
        }
    }
}
