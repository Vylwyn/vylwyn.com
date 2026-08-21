<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Default state: a published, live project.
     *
     * Factories exist so tests can say "give me a project" without caring about
     * the other fourteen columns. Every field gets a plausible value; states
     * below override only what a given test actually cares about.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->catchPhrase();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'tagline' => fake()->sentence(6),
            'summary' => fake()->paragraph(3),
            'body' => fake()->paragraphs(5, true),
            'status' => ProjectStatus::Live,
            'client' => fake()->optional()->company(),
            'year' => fake()->numberBetween(2019, 2026),
            'live_url' => fake()->url(),
            'repo_url' => null,
            'app_store_url' => null,
            'play_store_url' => null,
            'cover_image' => null,
            'is_featured' => false,
            'sort_order' => 0,
            'published_at' => now()->subDays(fake()->numberBetween(1, 365)),
        ];
    }

    /**
     * An unpublished draft. Should never appear on the public site.
     */
    public function draft(): static
    {
        return $this->state(fn (): array => [
            'published_at' => null,
        ]);
    }

    /**
     * Published with a future date — also should not appear publicly yet.
     */
    public function scheduled(): static
    {
        return $this->state(fn (): array => [
            'published_at' => now()->addWeek(),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (): array => [
            'is_featured' => true,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (): array => [
            'status' => ProjectStatus::InProgress,
            'live_url' => null,
        ]);
    }

    /**
     * A project with mobile apps on both stores.
     */
    public function withApps(): static
    {
        return $this->state(fn (): array => [
            'app_store_url' => 'https://apps.apple.com/app/id'.fake()->randomNumber(9),
            'play_store_url' => 'https://play.google.com/store/apps/details?id='.fake()->domainWord(),
        ]);
    }
}
