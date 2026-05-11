<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Letter;
use App\Models\Stakeholder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Letter>
 */
class LetterFactory extends Factory
{
    protected $model = Letter::class;

    public function definition(): array
    {
        $letterDate = fake()->dateTimeBetween('-90 days', 'now');

        return [
            'reference' => 'L-'.Str::upper(Str::random(8)),
            'title' => fake()->sentence(6),
            'subject' => fake()->sentence(8),
            'description' => fake()->paragraph(),
            'sender' => fake()->name(),
            'recipient' => fake()->name(),
            'letter_date' => $letterDate,
            'due_date' => fake()->boolean(60) ? fake()->dateTimeBetween($letterDate, '+60 days') : null,
            'priority' => fake()->randomElement(['LOW', 'MEDIUM', 'HIGH', 'URGENT']),
            'status' => fake()->randomElement(['DRAFT', 'PENDING', 'IN_PROGRESS', 'REVIEW', 'COMPLETED']),
            'stakeholder_id' => fn () => Stakeholder::query()->inRandomOrder()->first()?->id ?? Stakeholder::factory(),
            'department_id' => fn () => Department::query()->inRandomOrder()->first()?->id,
            'assigned_to' => fn () => User::query()->inRandomOrder()->first()?->id,
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
