<?php

namespace Database\Factories;

use App\Models\Stakeholder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Stakeholder>
 */
class StakeholderFactory extends Factory
{
    protected $model = Stakeholder::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();
        return [
            'name' => $name,
            'code' => Str::upper(Str::random(4)),
            'color' => fake()->hexColor(),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
