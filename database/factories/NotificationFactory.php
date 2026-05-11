<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => fn () => User::query()->inRandomOrder()->first()?->id ?? User::factory(),
            'type' => fake()->randomElement(['INFO', 'SUCCESS', 'WARNING', 'ERROR']),
            'title' => fake()->sentence(4),
            'message' => fake()->sentence(),
            'link' => fake()->boolean() ? fake()->url() : null,
            'is_read' => fake()->boolean(30),
        ];
    }

    public function unread(): static
    {
        return $this->state(fn () => ['is_read' => false]);
    }

    public function read(): static
    {
        return $this->state(fn () => ['is_read' => true]);
    }
}
