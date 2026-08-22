<?php

declare(strict_types=1);

use App\Enums\TechnologyCategory;

it('provides a label for every case', function (TechnologyCategory $category): void {
    expect($category->getLabel())->not->toBeEmpty();
})->with(TechnologyCategory::cases());

it('gives every category a distinct sort order', function (): void {
    $orders = array_map(
        fn (TechnologyCategory $category): int => $category->sortOrder(),
        TechnologyCategory::cases(),
    );

    // Duplicate sort orders would make the skills grid order non-deterministic.
    expect($orders)->toBe(array_unique($orders));
});

it('orders backend first', function (): void {
    $sorted = collect(TechnologyCategory::cases())
        ->sortBy(fn (TechnologyCategory $category): int => $category->sortOrder())
        ->values();

    expect($sorted->first())->toBe(TechnologyCategory::Backend);
});
