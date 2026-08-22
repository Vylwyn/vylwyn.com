<?php

declare(strict_types=1);

use App\Enums\ProjectStatus;

/**
 * A genuine unit test: no database, no HTTP, no Laravel container.
 *
 * Note these live outside the Feature suite, so they don't get RefreshDatabase
 * or the application TestCase — which is the point. They run in microseconds
 * because there is nothing to boot.
 */
it('is backed by readable strings', function (): void {
    expect(ProjectStatus::Live->value)->toBe('live')
        ->and(ProjectStatus::InProgress->value)->toBe('in_progress')
        ->and(ProjectStatus::Archived->value)->toBe('archived');
});

it('provides a human label for every case', function (ProjectStatus $status): void {
    expect($status->getLabel())->not->toBeEmpty();
})->with(ProjectStatus::cases());

it('provides a Filament colour for every case', function (ProjectStatus $status): void {
    expect($status->getColor())->toBeIn(['success', 'warning', 'gray', 'danger', 'info', 'primary']);
})->with(ProjectStatus::cases());

it('provides a Tailwind colour for every case', function (ProjectStatus $status): void {
    expect($status->tailwindColor())->not->toBeEmpty();
})->with(ProjectStatus::cases());

it('keeps admin and public colour vocabularies separate', function (): void {
    // Filament uses semantic names, the public site uses Tailwind families.
    expect(ProjectStatus::Live->getColor())->toBe('success')
        ->and(ProjectStatus::Live->tailwindColor())->toBe('emerald');
});
