<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users with varying balances
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'balance' => 2000,
        ]);

        User::factory()->create([
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'balance' => 1500,
        ]);

        User::factory()->create([
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'balance' => 3000,
        ]);

        User::factory()->create([
            'name' => 'Carol Davis',
            'email' => 'carol@example.com',
            'balance' => 500,
        ]);

        // Create additional random users
        User::factory(6)->create([
            'balance' => fn() => fake()->randomFloat(2, 100, 5000),
        ]);

        // Seed markets
        $this->call([
            MarketSeeder::class,
        ]);
    }
}
