<?php

declare(strict_types=1);

use App\Enums\ProjectStatus;
use App\Enums\TechnologyCategory;
use App\Models\Project;
use App\Models\Technology;

it('casts status to the ProjectStatus enum', function (): void {
    $project = Project::factory()->create(['status' => ProjectStatus::Live]);

    expect($project->fresh()->status)->toBe(ProjectStatus::Live);
});

it('uses the slug as its route key', function (): void {
    expect((new Project)->getRouteKeyName())->toBe('slug');
});

describe('the published scope', function (): void {
    it('includes projects published in the past', function (): void {
        Project::factory()->create();

        expect(Project::published()->count())->toBe(1);
    });

    it('excludes drafts', function (): void {
        Project::factory()->draft()->create();

        expect(Project::published()->count())->toBe(0);
    });

    it('excludes projects scheduled for the future', function (): void {
        Project::factory()->scheduled()->create();

        expect(Project::published()->count())->toBe(0);
    });
});

it('attaches technologies through the pivot table', function (): void {
    $project = Project::factory()->create();

    $laravel = Technology::factory()->create([
        'name' => 'Laravel',
        'slug' => 'laravel',
        'category' => TechnologyCategory::Backend,
    ]);

    $project->technologies()->attach($laravel);

    expect($project->technologies)->toHaveCount(1)
        ->and($project->technologies->first()->name)->toBe('Laravel')
        ->and($laravel->projects)->toHaveCount(1);
});

it('cannot attach the same technology twice', function (): void {
    $project = Project::factory()->create();
    $technology = Technology::factory()->create();

    $project->technologies()->attach($technology);

    // The composite primary key on the pivot enforces this at the database level.
    expect(fn () => $project->technologies()->attach($technology))
        ->toThrow(Illuminate\Database\UniqueConstraintViolationException::class);
});

describe('the links helper', function (): void {
    it('returns only populated links', function (): void {
        $project = Project::factory()->create([
            'live_url' => 'https://coolpexwaterfilters.com',
            'repo_url' => null,
            'app_store_url' => null,
            'play_store_url' => null,
        ]);

        expect($project->links())->toHaveCount(1)
            ->and($project->links()[0]['label'])->toBe('Live site');
    });

    it('includes both app stores when present', function (): void {
        $project = Project::factory()->withApps()->create();

        $labels = array_column($project->links(), 'label');

        expect($labels)->toContain('App Store', 'Google Play');
    });

    it('returns an empty array when nothing is set', function (): void {
        $project = Project::factory()->create([
            'live_url' => null,
            'repo_url' => null,
            'app_store_url' => null,
            'play_store_url' => null,
        ]);

        expect($project->links())->toBe([]);
    });
});

it('knows whether it has a case study', function (): void {
    expect(Project::factory()->create(['body' => null])->hasCaseStudy())->toBeFalse()
        ->and(Project::factory()->create(['body' => '# Heading'])->hasCaseStudy())->toBeTrue();
});
