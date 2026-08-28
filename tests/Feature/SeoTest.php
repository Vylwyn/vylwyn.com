<?php

declare(strict_types=1);

use App\Models\Project;

describe('meta tags', function (): void {
    it('sets a canonical url', function (): void {
        $this->get(route('home'))
            ->assertSee('<link rel="canonical" href="'.route('home').'"', escape: false);
    });

    it('uses an absolute og:image url', function (): void {
        $response = $this->get(route('home'));

        $response->assertSee('og:image', escape: false)
            ->assertSee(asset('og-image.png'), escape: false);
    });

    it('marks the homepage as a website and case studies as articles', function (): void {
        $this->get(route('home'))
            ->assertSee('<meta property="og:type" content="website">', escape: false);

        $project = Project::factory()->create(['body' => '## Something']);

        $this->get(route('projects.show', $project))
            ->assertSee('<meta property="og:type" content="article">', escape: false);
    });

    it('puts the project title in the page title', function (): void {
        $project = Project::factory()->create(['title' => 'Coolpex Water Filters']);

        $this->get(route('projects.show', $project))
            ->assertSee('<title>Coolpex Water Filters — ', escape: false);
    });
});

describe('structured data', function (): void {
    it('emits valid Person JSON-LD on the homepage', function (): void {
        $content = $this->get(route('home'))->getContent();

        expect($content)->toContain('application/ld+json');

        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $content, $matches);

        $data = json_decode(trim($matches[1]), true, flags: JSON_THROW_ON_ERROR);

        expect($data['@context'])->toBe('https://schema.org')
            ->and($data['@graph'][0]['@type'])->toBe('Person')
            ->and($data['@graph'][0]['name'])->toBe(config('portfolio.full_name'));
    });

    it('adds CreativeWork alongside Person on a case study', function (): void {
        $project = Project::factory()->create(['title' => 'RoomSphere', 'body' => '## Problem']);

        $content = $this->get(route('projects.show', $project))->getContent();

        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $content, $matches);

        $data = json_decode(trim($matches[1]), true, flags: JSON_THROW_ON_ERROR);
        $types = array_column($data['@graph'], '@type');

        expect($types)->toContain('Person', 'CreativeWork');
    });
});

describe('sitemap', function (): void {
    it('is served as xml', function (): void {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml');
    });

    it('always includes the homepage', function (): void {
        $this->get('/sitemap.xml')->assertSee(route('home'), escape: false);
    });

    it('includes published projects that have a case study', function (): void {
        $project = Project::factory()->create(['body' => '## Written']);

        $this->get('/sitemap.xml')->assertSee(route('projects.show', $project), escape: false);
    });

    it('excludes projects with no case study', function (): void {
        // Listing a page that renders nothing wastes crawl budget.
        $project = Project::factory()->create(['body' => null]);

        $this->get('/sitemap.xml')->assertDontSee(route('projects.show', $project), escape: false);
    });

    it('excludes drafts', function (): void {
        $project = Project::factory()->draft()->create(['body' => '## Written']);

        $this->get('/sitemap.xml')->assertDontSee(route('projects.show', $project), escape: false);
    });
});
