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

/**
 * Durations must match LinkedIn exactly — these are the real dates from the
 * profile, with the tenure LinkedIn displays for each.
 */
it('counts the final month inclusively, as LinkedIn does', function (
    string $start,
    string $end,
    string $expected,
): void {
    $experience = Experience::factory()->create([
        'started_on' => $start,
        'ended_on' => $end,
    ]);

    expect($experience->duration())->toBe($expected);
})->with([
    'Ali Alghanim & Sons' => ['2016-06-01', '2017-07-01', '1 yr 2 mos'],
    'Davlin Software' => ['2015-01-01', '2015-06-01', '6 mos'],
    'MSDI' => ['2011-08-01', '2012-09-01', '1 yr 2 mos'],
    'exactly one year' => ['2020-01-01', '2020-12-01', '1 yr'],
    'a single month' => ['2020-01-01', '2020-01-01', '1 mo'],
]);

it('does not pad a current role', function (): void {
    // "Present" is not a bounded month, so there is nothing to count inclusively.
    $experience = Experience::factory()->current()->create([
        'started_on' => now()->subYears(2)->subMonths(3)->startOfMonth(),
    ]);

    expect($experience->duration())->toBe('2 yrs 3 mos');
});

it('orders most recent first', function (): void {
    Experience::factory()->create(['started_on' => '2011-08-01', 'sort_order' => 4]);
    Experience::factory()->current()->create(['started_on' => '2017-07-01', 'sort_order' => 1]);

    expect(Experience::ordered()->first()->started_on->format('Y'))->toBe('2017');
});
