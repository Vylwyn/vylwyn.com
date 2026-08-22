<?php

declare(strict_types=1);

use App\Enums\TechnologyCategory;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Technology;

it('loads successfully', function (): void {
    $this->get(route('home'))->assertOk();
});

it('shows featured published projects', function (): void {
    $featured = Project::factory()->featured()->create(['title' => 'Coolpex Water Filters']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee($featured->title)
        ->assertSee($featured->summary);
});

it('hides projects that are not featured', function (): void {
    Project::factory()->create(['title' => 'Not On The Homepage']);

    $this->get(route('home'))->assertDontSee('Not On The Homepage');
});

it('hides unpublished projects even when featured', function (): void {
    Project::factory()->featured()->draft()->create(['title' => 'Secret Draft']);

    $this->get(route('home'))->assertDontSee('Secret Draft');
});

it('lists experiences with their formatted period', function (): void {
    Experience::factory()->current()->create([
        'role' => 'Information Technology Team Lead',
        'organisation' => 'Alghanim International',
        'started_on' => '2017-07-01',
    ]);

    $this->get(route('home'))
        ->assertSee('Information Technology Team Lead')
        ->assertSee('Alghanim International')
        ->assertSee('Jul 2017 — Present', escape: false);
});

it('shows technologies flagged for the skills grid', function (): void {
    Technology::factory()->create([
        'name' => 'Laravel',
        'category' => TechnologyCategory::Backend,
    ]);

    Technology::factory()->hiddenFromSkills()->create([
        'name' => 'Offline sync',
        'category' => TechnologyCategory::Mobile,
    ]);

    $response = $this->get(route('home'));

    $response->assertSee('Laravel')->assertDontSee('Offline sync');
});

it('renders an empty state when there are no projects', function (): void {
    $this->get(route('home'))->assertSee('No published projects yet');
});
