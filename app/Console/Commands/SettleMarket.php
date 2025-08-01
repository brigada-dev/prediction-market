<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Market;
use App\Services\MarketMaker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SettleMarket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:settle 
                            {market? : Market ID to settle}
                            {--outcome= : Force outcome (yes/no) - for testing only}
                            {--auto : Automatically settle all expired markets}
                            {--dry-run : Show what would happen without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Settle prediction markets and distribute payouts to winners';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $marketId = $this->argument('market');
        $forcedOutcome = $this->option('outcome');
        $auto = $this->option('auto');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🧪 DRY RUN MODE - No changes will be made');
        }

        if ($auto) {
            return $this->settleExpiredMarkets($dryRun);
        }

        if (!$marketId) {
            $this->error('❌ Please provide a market ID or use --auto flag');
            return Command::FAILURE;
        }

        return $this->settleSpecificMarket((int) $marketId, $forcedOutcome, $dryRun);
    }

    /**
     * Settle a specific market.
     */
    private function settleSpecificMarket(int $marketId, ?string $forcedOutcome, bool $dryRun): int
    {
        try {
            $market = Market::with(['positions.user'])->findOrFail($marketId);

            $this->info("🎯 Settling Market: {$market->title}");
            $this->line("📅 Created: {$market->created_at->format('Y-m-d H:i')}");
            $this->line("⏰ Closes: {$market->closes_at->format('Y-m-d H:i')}");

            if ($market->resolved) {
                $this->warn("⚠️  Market already resolved with outcome: {$market->outcome}");
                return Command::SUCCESS;
            }

            // Validate forced outcome
            if ($forcedOutcome && !in_array($forcedOutcome, ['yes', 'no'])) {
                $this->error('❌ Outcome must be "yes" or "no"');
                return Command::FAILURE;
            }

            // For testing, allow forced outcome; in production, this would come from admin or oracle
            $outcome = $forcedOutcome;
            if (!$outcome) {
                $outcome = $this->choice(
                    'What is the market outcome?',
                    ['yes', 'no'],
                    0
                );
            }

            $this->line("🎲 Market outcome: " . strtoupper($outcome));

            // Get market statistics
            $marketMaker = app(MarketMaker::class);
            $stats = $marketMaker->getMarketStats($market);

            $this->table(
                ['Metric', 'Value'],
                [
                    ['Total Positions', $stats['total_positions']],
                    ['Total Volume', number_format($stats['total_volume'], 2) . ' shares'],
                    ['YES Shares', number_format($stats['liquidity']['yes'], 2)],
                    ['NO Shares', number_format($stats['liquidity']['no'], 2)],
                    ['YES Probability', $stats['probability_yes'] . '%'],
                    ['NO Probability', $stats['probability_no'] . '%'],
                ]
            );

            // Calculate payouts
            $winningPositions = $market->positions->where('choice', $outcome);
            $losingPositions = $market->positions->where('choice', '!=', $outcome);

            $totalWinners = $winningPositions->count();
            $totalLosers = $losingPositions->count();
            $totalWinningShares = $winningPositions->sum('shares');
            $totalPayout = $totalWinningShares; // €1 per winning share

            $this->newLine();
            $this->info("💰 Settlement Summary:");
            $this->line("🏆 Winning choice: " . strtoupper($outcome));
            $this->line("👥 Winners: {$totalWinners} positions");
            $this->line("👥 Losers: {$totalLosers} positions");
            $this->line("📊 Winning shares: " . number_format($totalWinningShares, 2));
                                $this->line("💵 Total payout: €" . number_format($totalPayout, 2));

            if ($winningPositions->count() > 0) {
                $this->newLine();
                $this->info("🏆 Winners:");
                $winnersTable = [];
                foreach ($winningPositions as $position) {
                    $shares = (float) $position->shares;
                    $cost = (float) $position->cost;
                    $payout = $shares;
                    $winnersTable[] = [
                        $position->user->name,
                        number_format($shares, 2),
                        '€' . number_format($cost, 2),
                        '€' . number_format($payout, 2),
                        '€' . number_format($payout - $cost, 2),
                    ];
                }

                $this->table(
                    ['User', 'Shares', 'Cost', 'Payout', 'Profit/Loss'],
                    $winnersTable
                );
            }

            if (!$dryRun) {
                if ($this->confirm('Proceed with settlement?', true)) {
                    DB::transaction(function () use ($market, $outcome, $marketMaker) {
                        // Mark market as resolved
                        $market->update([
                            'resolved' => true,
                            'outcome' => $outcome,
                        ]);

                        // Settle via MarketMaker
                        $marketMaker->settleMarket($market);
                    });

                    $this->info("✅ Market settled successfully!");
                    $this->line("🆔 Market ID: {$market->id}");
                    $this->line("🎯 Outcome: " . strtoupper($outcome));
                    $this->line("💰 Payouts distributed to {$totalWinners} winners");
                } else {
                    $this->info("Settlement cancelled");
                }
            } else {
                $this->info("🧪 DRY RUN: Settlement would proceed with outcome '{$outcome}'");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Error settling market: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Automatically settle all expired markets.
     */
    private function settleExpiredMarkets(bool $dryRun): int
    {
        $expiredMarkets = Market::where('resolved', false)
            ->where('closes_at', '<', now())
            ->with(['positions.user'])
            ->get();

        if ($expiredMarkets->isEmpty()) {
            $this->info("✅ No expired markets to settle");
            return Command::SUCCESS;
        }

        $this->info("🔍 Found {$expiredMarkets->count()} expired markets");

        foreach ($expiredMarkets as $market) {
            $this->newLine();
            $this->info("🎯 Market: {$market->title}");
            $this->line("⏰ Expired: {$market->closes_at->diffForHumans()}");

            if (!$dryRun) {
                // In a real system, you'd have oracle data or admin resolution
                // For this demo, we'll simulate random outcomes for expired markets
                $outcome = rand(0, 1) ? 'yes' : 'no';
                $this->line("🎲 Simulated outcome: " . strtoupper($outcome));

                try {
                    DB::transaction(function () use ($market, $outcome) {
                        $market->update([
                            'resolved' => true,
                            'outcome' => $outcome,
                        ]);

                        app(MarketMaker::class)->settleMarket($market);
                    });

                    $this->line("✅ Settled with outcome: " . strtoupper($outcome));
                } catch (\Exception $e) {
                    $this->error("❌ Failed to settle: " . $e->getMessage());
                }
            } else {
                $this->line("🧪 DRY RUN: Would settle this market");
            }
        }

        if (!$dryRun) {
            $this->newLine();
            $this->info("✅ Auto-settlement complete!");
        }

        return Command::SUCCESS;
    }
}
