<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TechnologyCategory;
use App\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Technology>
 */
class TechnologyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'category' => fake()->randomElement(TechnologyCategory::cases()),
            'show_in_skills' => true,
            'sort_order' => 0,
        ];
    }

    public function category(TechnologyCategory $category): static
    {
        return $this->state(fn (): array => [
            'category' => $category,
        ]);
    }

    /**
     * A project tag that should not clutter the public skills grid.
     */
    public function hiddenFromSkills(): static
    {
        return $this->state(fn (): array => [
            'show_in_skills' => false,
        ]);
    }
}
