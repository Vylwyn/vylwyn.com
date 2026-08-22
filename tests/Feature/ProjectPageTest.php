<?php

declare(strict_types=1);

use App\Models\Project;

it('shows a published project', function (): void {
    $project = Project::factory()->create([
        'title' => 'Coolpex Water Filters',
        'slug' => 'coolpex-water-filters',
        'body' => '## The problem',
    ]);

    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertSee('Coolpex Water Filters')
        ->assertSee('The problem');
});

it('resolves the project by slug rather than id', function (): void {
    $project = Project::factory()->create(['slug' => 'coolpex-water-filters']);

    expect(route('projects.show', $project))->toContain('/work/coolpex-water-filters');
});

it('renders markdown in the body as html', function (): void {
    $project = Project::factory()->create([
        'body' => "## Constraints\n\nRunning on **shared hosting**.",
    ]);

    $this->get(route('projects.show', $project))
        ->assertSee('<h2>Constraints</h2>', escape: false)
        ->assertSee('<strong>shared hosting</strong>', escape: false);
});

it('returns 404 for a draft', function (): void {
    $project = Project::factory()->draft()->create();

    $this->get(route('projects.show', $project))->assertNotFound();
});

it('returns 404 for a project scheduled in the future', function (): void {
    $project = Project::factory()->scheduled()->create();

    $this->get(route('projects.show', $project))->assertNotFound();
});

it('returns 404 for an unknown slug', function (): void {
    $this->get('/work/does-not-exist')->assertNotFound();
});

it('does not list itself under more work', function (): void {
    $project = Project::factory()->create(['title' => 'The Current One']);
    Project::factory()->create(['title' => 'Another Project']);

    /**
     * Asserting against the view data rather than counting strings in the HTML —
     * the title legitimately appears in <title>, og:title and the <h1>, so a
     * substring count tests the template, not the behaviour we care about.
     */
    $this->get(route('projects.show', $project))
        ->assertOk()
        ->assertSee('Another Project')
        ->assertViewHas('related', fn ($related): bool => ! $related->contains($project));
});

it('limits related projects to two', function (): void {
    $project = Project::factory()->create();
    Project::factory()->count(5)->create();

    $this->get(route('projects.show', $project))
        ->assertViewHas('related', fn ($related): bool => $related->count() === 2);
});

it('excludes drafts from related projects', function (): void {
    $project = Project::factory()->create();
    Project::factory()->draft()->create(['title' => 'Hidden Draft']);

    $this->get(route('projects.show', $project))
        ->assertDontSee('Hidden Draft');
});
