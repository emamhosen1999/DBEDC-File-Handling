<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'user_id' => fn () => User::query()->inRandomOrder()->first()?->id ?? User::factory(),
            'action' => fake()->randomElement(['created', 'updated', 'deleted', 'viewed']),
            'entity_type' => fake()->randomElement(['Letter', 'Task', 'User', 'Department']),
            'entity_id' => null,
            'description' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
