<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Market>
 */
class MarketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence() . '?',
            'description' => fake()->paragraph(),
            'closes_at' => fake()->dateTimeBetween('now', '+1 year'),
            'resolved' => false,
            'outcome' => 'unknown',
            'liquidity' => fake()->randomFloat(2, 1000, 10000),
            'b' => fake()->randomFloat(2, 50, 300),
        ];
    }
}
