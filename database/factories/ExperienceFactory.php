<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedOn = fake()->dateTimeBetween('-12 years', '-2 years');

        return [
            'role' => fake()->jobTitle(),
            'organisation' => fake()->company(),
            'location' => fake()->city().', '.fake()->country(),
            'started_on' => $startedOn,
            'ended_on' => fake()->dateTimeBetween($startedOn, '-1 month'),
            'summary' => fake()->paragraph(),
            'sort_order' => 0,
        ];
    }

    /**
     * The role held right now — a null end date is what marks it current.
     */
    public function current(): static
    {
        return $this->state(fn (): array => [
            'ended_on' => null,
        ]);
    }
}
