<?php

declare(strict_types=1);

use App\Models\SiteContent;
use Illuminate\Support\Facades\Cache;

it('renders edited hero copy on the homepage', function (): void {
    SiteContent::create([
        'hero_name' => 'Vylwyn D’Souza',
        'hero_role' => 'Something Entirely New',
        'hero_tagline_lead' => 'A brand new tagline.',
        'hero_lede' => 'A brand new supporting paragraph.',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Something Entirely New')
        ->assertSee('A brand new tagline.')
        ->assertSee('A brand new supporting paragraph.');
});

it('renders the about body as markdown', function (): void {
    SiteContent::create([
        'hero_name' => 'Vylwyn',
        'hero_role' => 'Role',
        'hero_tagline_lead' => 'Lead',
        'about_body' => "I lead a team at **Alghanim International**.\n\nSecond paragraph.",
    ]);

    $this->get(route('home'))
        ->assertSee('<strong>Alghanim International</strong>', escape: false)
        ->assertSee('Second paragraph.');
});

it('falls back to config when no row exists', function (): void {
    // A fresh install must still render sensibly rather than showing blanks.
    expect(SiteContent::query()->count())->toBe(0);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee(config('portfolio.role'));
});

describe('caching', function (): void {
    it('caches the singleton', function (): void {
        Cache::forget('site-content');

        SiteContent::create([
            'hero_name' => 'Cached Name',
            'hero_role' => 'Role',
            'hero_tagline_lead' => 'Lead',
        ]);

        SiteContent::current();

        expect(Cache::has('site-content'))->toBeTrue();
    });

    it('clears the cache when content is saved', function (): void {
        $content = SiteContent::create([
            'hero_name' => 'Original',
            'hero_role' => 'Role',
            'hero_tagline_lead' => 'Lead',
        ]);

        SiteContent::current();

        $content->update(['hero_name' => 'Updated']);

        // Without cache invalidation on save, the admin would appear to do
        // nothing — the classic caching bug.
        expect(SiteContent::current()->hero_name)->toBe('Updated');
    });

    it('shows updated copy on the site immediately after an edit', function (): void {
        $content = SiteContent::create([
            'hero_name' => 'Before Edit',
            'hero_role' => 'Role',
            'hero_tagline_lead' => 'Lead',
        ]);

        $this->get(route('home'))->assertSee('Before Edit');

        $content->update(['hero_name' => 'After Edit']);

        $this->get(route('home'))
            ->assertSee('After Edit')
            ->assertDontSee('Before Edit');
    });
});
