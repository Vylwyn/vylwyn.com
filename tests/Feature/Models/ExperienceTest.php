<?php

declare(strict_types=1);

use App\Models\Experience;

it('treats a null end date as the current role', function (): void {
    $current = Experience::factory()->current()->create();
    $past = Experience::factory()->create();

    expect($current->isCurrent())->toBeTrue()
        ->and($past->isCurrent())->toBeFalse();
});

it('formats the period with Present for the current role', function (): void {
    $experience = Experience::factory()->current()->create([
        'started_on' => '2017-07-01',
    ]);

    expect($experience->period())->toBe('Jul 2017 — Present');
});

it('formats the period with both dates for a past role', function (): void {
    $experience = Experience::factory()->create([
        'started_on' => '2016-06-01',
        'ended_on' => '2017-07-01',
    ]);

    expect($experience->period())->toBe('Jun 2016 — Jul 2017');
});

it('calculates duration in years and months', function (): void {
    $experience = Experience::factory()->create([
        'started_on' => '2016-06-01',
        'ended_on' => '2017-08-01',
    ]);

    expect($experience->duration())->toBe('1 yr 2 mos');
});

it('omits the year component for short roles', function (): void {
    $experience = Experience::factory()->create([
        'started_on' => '2015-01-01',
        'ended_on' => '2015-07-01',
    ]);

    expect($experience->duration())->toBe('6 mos');
});

it('orders most recent first', function (): void {
    Experience::factory()->create(['started_on' => '2011-08-01', 'sort_order' => 4]);
    Experience::factory()->current()->create(['started_on' => '2017-07-01', 'sort_order' => 1]);

    expect(Experience::ordered()->first()->started_on->format('Y'))->toBe('2017');
});
