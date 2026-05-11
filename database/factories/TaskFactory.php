<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Letter;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'letter_id' => fn () => Letter::query()->inRandomOrder()->first()?->id,
            'assigned_to' => fn () => User::query()->inRandomOrder()->first()?->id,
            'department_id' => fn () => Department::query()->inRandomOrder()->first()?->id,
            'status' => fake()->randomElement(['PENDING', 'IN_PROGRESS', 'REVIEW', 'COMPLETED']),
            'priority' => fake()->randomElement(['LOW', 'MEDIUM', 'HIGH', 'URGENT']),
            'due_date' => fake()->boolean(70) ? fake()->dateTimeBetween('now', '+30 days') : null,
            'created_by' => fn () => User::query()->inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => 'COMPLETED',
            'completed_at' => now(),
        ]);
    }
}
