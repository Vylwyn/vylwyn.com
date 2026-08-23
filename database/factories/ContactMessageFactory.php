<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'subject' => fake()->sentence(4),
            'message' => fake()->paragraph(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'read_at' => null,
            'notified' => true,
        ];
    }

    public function read(): static
    {
        return $this->state(fn (): array => [
            'read_at' => now()->subHour(),
        ]);
    }

    /**
     * Saved, but the notification email failed to send.
     */
    public function notNotified(): static
    {
        return $this->state(fn (): array => [
            'notified' => false,
        ]);
    }
}
