<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Market;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MakeMarket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:market 
                            {title : The market title/question}
                            {--desc= : Market description}
                            {--hours=24 : Hours until market closes}
                            {--liquidity=1000 : Initial liquidity parameter}
                            {--b=100 : LMSR b parameter for price sensitivity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new prediction market with specified parameters';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $title = $this->argument('title');
        $description = $this->option('desc') ?? 'No description provided.';
        $hours = (int) $this->option('hours');
        $liquidity = (float) $this->option('liquidity');
        $b = (float) $this->option('b');

        // Validate inputs
        if (empty($title)) {
            $this->error('Market title cannot be empty.');
            return Command::FAILURE;
        }

        if ($hours <= 0) {
            $this->error('Hours must be greater than 0.');
            return Command::FAILURE;
        }

        if ($liquidity <= 0) {
            $this->error('Liquidity must be greater than 0.');
            return Command::FAILURE;
        }

        if ($b <= 0) {
            $this->error('Parameter b must be greater than 0.');
            return Command::FAILURE;
        }

        try {
            $market = Market::create([
                'title' => $title,
                'description' => $description,
                'closes_at' => now()->addHours($hours),
                'resolved' => false,
                'outcome' => 'unknown',
                'liquidity' => $liquidity,
                'b' => $b,
            ]);

            $this->info("✅ Market created successfully!");
            $this->line("🆔 ID: {$market->id}");
            $this->line("📋 Title: {$market->title}");
            $this->line("📝 Description: {$market->description}");
            $this->line("⏰ Closes at: {$market->closes_at->format('Y-m-d H:i:s T')}");
            $this->line("💰 Liquidity: {$market->liquidity}");
            $this->line("📊 B Parameter: {$market->b}");

            // Show how many hours until it closes
            $hoursUntilClose = now()->diffInHours($market->closes_at, false);
            if ($hoursUntilClose > 0) {
                $this->line("⏳ Closes in {$hoursUntilClose} hours");
            } else {
                $this->warn("⚠️  Market closes in the past!");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Failed to create market: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
