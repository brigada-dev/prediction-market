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
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'balance' => 10000,
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        // Create test users with varying balances
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'balance' => 2000,
            'role' => 'user',
        ]);

        User::factory()->create([
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'balance' => 1500,
            'role' => 'user',
        ]);

        User::factory()->create([
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'balance' => 3000,
            'role' => 'user',
        ]);

        User::factory()->create([
            'name' => 'Carol Davis',
            'email' => 'carol@example.com',
            'balance' => 500,
            'role' => 'user',
        ]);

        // Create additional random users
        User::factory(6)->create([
            'balance' => fn() => fake()->randomFloat(2, 100, 5000),
            'role' => 'user',
        ]);

        // Seed markets
        $this->call([
            MarketSeeder::class,
        ]);
    }
}
