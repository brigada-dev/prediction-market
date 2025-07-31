<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Market;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Position>
 */
class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'market_id' => Market::factory(),
            'choice' => fake()->randomElement(['yes', 'no']),
            'shares' => fake()->randomFloat(4, 1, 1000),
            'cost' => fake()->randomFloat(4, 1, 1000),
        ];
    }
}
