<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Market;
use App\Models\Position;
use Illuminate\Support\Facades\Cache;

class MarketMaker
{
    /**
     * Get current liquidity (total shares) for each side of a market.
     */
    public function getLiquidity(Market $market): array
    {
        $cacheKey = "market_liquidity_{$market->id}";

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($market) {
            // Multi-choice support: sum by choice_id when choices exist, else classic yes/no
            if ($market->choices()->exists()) {
                $totals = [];
                foreach ($market->choices as $choice) {
                    $totals[$choice->slug] = (float) Position::where('market_id', $market->id)
                        ->where('choice_id', $choice->id)
                        ->sum('shares');
                }
                return $totals;
            }

            $yes = Position::where('market_id', $market->id)
                ->where('choice', 'yes')
                ->sum('shares');

            $no = Position::where('market_id', $market->id)
                ->where('choice', 'no')
                ->sum('shares');

            return [
                'yes' => (float) $yes,
                'no' => (float) $no,
            ];
        });
    }

    /**
     * Calculate current market prices using LMSR.
     * Returns probability-based prices between 0 and 1.
     */
    public function price(Market $market): array
    {
        $b = (float) $market->b;
        $liquidity = $this->getLiquidity($market);

        // Handle edge case where b is 0 or very small
        if ($b <= 0) {
            return ['yes' => 0.5, 'no' => 0.5];
        }

        // Multi-choice: compute exp(q_i/b) for each outcome
        if ($market->choices()->exists()) {
            $expTotals = [];
            foreach ($market->choices as $choice) {
                $q = $liquidity[$choice->slug] ?? 0.0;
                $expTotals[$choice->slug] = exp($q / $b);
            }
            $sum = array_sum($expTotals) ?: 1;
            $prices = [];
            foreach ($expTotals as $slug => $val) {
                $prices[$slug] = round($val / $sum, 4);
            }
            return $prices;
        }

        $eYes = exp(($liquidity['yes'] ?? 0) / $b);
        $eNo = exp(($liquidity['no'] ?? 0) / $b);

        $sum = $eYes + $eNo;

        // Prevent division by zero
        if ($sum == 0) {
            return ['yes' => 0.5, 'no' => 0.5];
        }

        return [
            'yes' => round($eYes / $sum, 4),
            'no' => round($eNo / $sum, 4),
        ];
    }

    /**
     * Calculate the cost to buy a specified number of shares.
     * Uses LMSR cost function: C(q) = b * ln(e^(q_yes/b) + e^(q_no/b))
     */
    public function costToBuy(Market $market, string $choice, float $shares): float
    {
        if ($shares <= 0) {
            return 0.0;
        }

        $b = (float) $market->b;
        $liquidity = $this->getLiquidity($market);

        // Multi-choice path when choices exist: treat choice as slug
        if ($market->choices()->exists()) {
            $qBefore = [];
            foreach ($market->choices as $c) {
                $qBefore[$c->slug] = (float) ($liquidity[$c->slug] ?? 0.0);
            }
            $qAfter = $qBefore;
            if (isset($qAfter[$choice])) {
                $qAfter[$choice] += $shares;
            }

            $sumExpBefore = 0.0; $sumExpAfter = 0.0;
            foreach ($qBefore as $q) { $sumExpBefore += exp($q / $b); }
            foreach ($qAfter as $q) { $sumExpAfter += exp($q / $b); }

            $costBefore = $b * log($sumExpBefore);
            $costAfter = $b * log($sumExpAfter);
            $cost = $costAfter - $costBefore;
            return round(max(0.01, $cost), 4);
        }

        $qYesBefore = (float) ($liquidity['yes'] ?? 0);
        $qNoBefore = (float) ($liquidity['no'] ?? 0);

        if ($choice === 'yes') {
            $qYesAfter = $qYesBefore + $shares;
            $qNoAfter = $qNoBefore;
        } else {
            $qYesAfter = $qYesBefore;
            $qNoAfter = $qNoBefore + $shares;
        }

        $costBefore = $b * log(exp($qYesBefore / $b) + exp($qNoBefore / $b));
        $costAfter = $b * log(exp($qYesAfter / $b) + exp($qNoAfter / $b));

        $cost = $costAfter - $costBefore;

        return round(max(0.01, $cost), 4); // Minimum cost of 0.01
    }

    /**
     * Calculate the payout for selling shares back to the market.
     */
    public function costToSell(Market $market, string $choice, float $shares): float
    {
        if ($shares <= 0) {
            return 0.0;
        }

        $b = (float) $market->b;
        $liquidity = $this->getLiquidity($market);

        $qYesBefore = $liquidity['yes'];
        $qNoBefore = $liquidity['no'];

        // Calculate new quantities after sale
        if ($choice === 'yes') {
            $qYesAfter = max(0, $qYesBefore - $shares);
            $qNoAfter = $qNoBefore;
        } else {
            $qYesAfter = $qYesBefore;
            $qNoAfter = max(0, $qNoBefore - $shares);
        }

        // LMSR cost function (reverse for selling)
        $costBefore = $b * log(exp($qYesBefore / $b) + exp($qNoBefore / $b));
        $costAfter = $b * log(exp($qYesAfter / $b) + exp($qNoAfter / $b));

        $payout = $costBefore - $costAfter;

        return round(max(0, $payout), 4);
    }

    /**
     * Get market statistics for display.
     */
    public function getMarketStats(Market $market): array
    {
        $market->loadMissing('choices');
        $liquidity = $this->getLiquidity($market);
        $prices = $this->price($market);

        $totalPositions = Position::where('market_id', $market->id)->count();

        // Multi-choice markets
        if ($market->choices->isNotEmpty()) {
            $totalVolume = array_sum(array_map(fn($v) => (float) $v, $liquidity));
            // Probabilities per outcome in percent
            $choiceProbabilities = [];
            foreach ($prices as $slug => $p) {
                $choiceProbabilities[$slug] = round(((float) $p) * 100, 1);
            }
            // Determine top outcome
            arsort($choiceProbabilities);
            $topSlug = array_key_first($choiceProbabilities);
            $topChoice = $market->choices->firstWhere('slug', $topSlug);

            return [
                'liquidity' => $liquidity,
                'prices' => $prices,
                'total_volume' => round($totalVolume, 2),
                'total_positions' => $totalPositions,
                'probability_yes' => null,
                'probability_no' => null,
                'choice_probabilities' => $choiceProbabilities,
                'top_choice' => $topSlug,
                'top_choice_name' => $topChoice?->name,
                'top_probability' => $topSlug !== null ? $choiceProbabilities[$topSlug] : null,
            ];
        }

        // Binary markets (yes/no)
        $totalVolume = (float) ($liquidity['yes'] ?? 0) + (float) ($liquidity['no'] ?? 0);
        return [
            'liquidity' => $liquidity,
            'prices' => $prices,
            'total_volume' => round($totalVolume, 2),
            'total_positions' => $totalPositions,
            'probability_yes' => round(($prices['yes'] ?? 0) * 100, 1),
            'probability_no' => round(($prices['no'] ?? 0) * 100, 1),
        ];
    }

    /**
     * Get historical price data for chart display.
     */
    public function getHistoricalPrices(Market $market, int $hours = 24): array
    {
        $cacheKey = "market_historical_prices_{$market->id}_{$hours}h";
        
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($market, $hours) {
            // Get positions over the specified time period
            $startTime = now()->subHours($hours);
            $positions = $market->positions()
                ->where('created_at', '>=', $startTime)
                ->orderBy('created_at')
                ->get();
            
            if ($positions->isEmpty()) {
                // Return current prices if no historical data
                $currentPrices = $this->price($market);
                $now = now();
                return [
                    'timestamps' => [$now->subHour()->toISOString(), $now->toISOString()],
                    'prices' => [$currentPrices, $currentPrices]
                ];
            }
            
            // Calculate price evolution over time
            $timePoints = [];
            $priceHistory = [];
            
            // Add initial point (current prices before any trades in period)
            $initialTime = $startTime;
            $initialPrices = $this->calculatePricesAtTime($market, $initialTime);
            $timePoints[] = $initialTime->toISOString();
            $priceHistory[] = $initialPrices;
            
            // Calculate running liquidity and prices after each trade
            $runningLiquidity = $this->getLiquidityAtTime($market, $initialTime);
            
            foreach ($positions as $position) {
                // Update running liquidity
                if ($market->choices()->exists()) {
                    $choice = $position->marketChoice;
                    if ($choice) {
                        $runningLiquidity[$choice->slug] = ($runningLiquidity[$choice->slug] ?? 0) + $position->shares;
                    }
                } else {
                    $runningLiquidity[$position->choice] = ($runningLiquidity[$position->choice] ?? 0) + $position->shares;
                }
                
                // Calculate prices at this point
                $prices = $this->calculatePricesFromLiquidity($market, $runningLiquidity);
                $timePoints[] = $position->created_at->toISOString();
                $priceHistory[] = $prices;
            }
            
            // Add current point
            $currentPrices = $this->price($market);
            $timePoints[] = now()->toISOString();
            $priceHistory[] = $currentPrices;
            
            return [
                'timestamps' => $timePoints,
                'prices' => $priceHistory
            ];
        });
    }
    
    /**
     * Get liquidity state at a specific time.
     */
    private function getLiquidityAtTime(Market $market, $time): array
    {
        $positions = $market->positions()
            ->where('created_at', '<', $time)
            ->get();
        
        if ($market->choices()->exists()) {
            $liquidity = [];
            foreach ($market->choices as $choice) {
                $liquidity[$choice->slug] = (float) $positions
                    ->where('choice_id', $choice->id)
                    ->sum('shares');
            }
            return $liquidity;
        }
        
        return [
            'yes' => (float) $positions->where('choice', 'yes')->sum('shares'),
            'no' => (float) $positions->where('choice', 'no')->sum('shares'),
        ];
    }
    
    /**
     * Calculate prices at a specific time.
     */
    private function calculatePricesAtTime(Market $market, $time): array
    {
        $liquidity = $this->getLiquidityAtTime($market, $time);
        return $this->calculatePricesFromLiquidity($market, $liquidity);
    }
    
    /**
     * Calculate prices from given liquidity state.
     */
    private function calculatePricesFromLiquidity(Market $market, array $liquidity): array
    {
        $b = (float) $market->b;
        
        if ($b <= 0) {
            return $market->choices()->exists() 
                ? array_fill_keys($market->choices->pluck('slug')->toArray(), 0.5)
                : ['yes' => 0.5, 'no' => 0.5];
        }
        
        if ($market->choices()->exists()) {
            $expTotals = [];
            foreach ($market->choices as $choice) {
                $q = $liquidity[$choice->slug] ?? 0.0;
                $expTotals[$choice->slug] = exp($q / $b);
            }
            $sum = array_sum($expTotals) ?: 1;
            $prices = [];
            foreach ($expTotals as $slug => $val) {
                $prices[$slug] = round($val / $sum, 4);
            }
            return $prices;
        }
        
        $eYes = exp(($liquidity['yes'] ?? 0) / $b);
        $eNo = exp(($liquidity['no'] ?? 0) / $b);
        $sum = $eYes + $eNo;
        
        if ($sum == 0) {
            return ['yes' => 0.5, 'no' => 0.5];
        }
        
        return [
            'yes' => round($eYes / $sum, 4),
            'no' => round($eNo / $sum, 4),
        ];
    }

    /**
     * Clear market cache when positions change.
     */
    public function clearMarketCache(Market $market): void
    {
        Cache::forget("market_liquidity_{$market->id}");
        // Clear historical price caches
        for ($hours = 1; $hours <= 168; $hours *= 2) { // 1h, 2h, 4h, 8h, 16h, 32h, 64h, 128h (5+ days)
            Cache::forget("market_historical_prices_{$market->id}_{$hours}h");
        }
        Cache::forget("market_historical_prices_{$market->id}_24h");
    }

    /**
     * Validate if a trade is possible given market constraints.
     */
    public function validateTrade(Market $market, string $choice, float $shares, float $userBalance): array
    {
        $errors = [];
        
        if ($market->choices()->exists()) {
            $validSlugs = $market->choices->pluck('slug')->all();
            if (!in_array($choice, $validSlugs, true)) {
                $errors[] = 'Invalid choice for this market.';
            }
        } else {
            if (!in_array($choice, ['yes', 'no'], true)) {
                $errors[] = 'Invalid choice. Must be "yes" or "no".';
            }
        }

        if ($shares <= 0) {
            $errors[] = 'Shares must be greater than 0.';
        }

        if ($shares > 1000) {
            $errors[] = 'Maximum 1000 shares per trade.';
        }

        if ($market->isClosed()) {
            $errors[] = 'Market is closed for trading.';
        }

        $cost = $this->costToBuy($market, $choice, $shares);
        if ($userBalance < $cost) {
            $errors[] = "Insufficient balance. Cost: $cost, Balance: $userBalance";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'cost' => $cost ?? 0,
        ];
    }

    /**
     * Settle a resolved market by paying out winning positions.
     */
    public function settleMarket(Market $market): array
    {
        if (!$market->resolved || $market->outcome === 'unknown') {
            throw new \InvalidArgumentException('Market must be resolved with a valid outcome to settle.');
        }

        $winningPositions = Position::where('market_id', $market->id)
            ->where('choice', $market->outcome)
            ->with('user')
            ->get();

        $losingPositions = Position::where('market_id', $market->id)
            ->where('choice', '!=', $market->outcome)
            ->with('user')
            ->get();

        $settlementSummary = [
            'market_id' => $market->id,
            'outcome' => $market->outcome,
            'total_winners' => $winningPositions->count(),
            'total_losers' => $losingPositions->count(),
            'total_winning_shares' => $winningPositions->sum('shares'),
            'total_losing_shares' => $losingPositions->sum('shares'),
            'total_payout' => 0,
            'winners' => [],
            'settled_at' => now(),
        ];

        foreach ($winningPositions as $position) {
            // Pay out €1 per share for winning positions
            $payout = (float) $position->shares;
            $profit = $payout - (float) $position->cost;
            
            $position->user->increment('balance', $payout);
            
            $settlementSummary['winners'][] = [
                'user_id' => $position->user_id,
                'user_name' => $position->user->name,
                'shares' => (float) $position->shares,
                'cost' => (float) $position->cost,
                'payout' => $payout,
                'profit' => $profit,
                'return_rate' => $position->cost > 0 ? ($profit / (float) $position->cost) * 100 : 0,
            ];
            
            $settlementSummary['total_payout'] += $payout;
        }

        // Calculate market efficiency metrics
        $finalPrice = $this->price($market)[$market->outcome];
        $marketAccuracy = $this->calculateMarketAccuracy($market);
        
        $settlementSummary['market_metrics'] = [
            'final_price' => $finalPrice,
            'market_accuracy' => $marketAccuracy,
            'total_volume' => $winningPositions->sum('shares') + $losingPositions->sum('shares'),
            'liquidity_utilization' => $this->calculateLiquidityUtilization($market),
        ];

        // Clear market cache after settlement
        $this->clearMarketCache($market);

        return $settlementSummary;
    }

    /**
     * Calculate market accuracy based on final price vs actual outcome.
     */
    private function calculateMarketAccuracy(Market $market): float
    {
        $finalPrices = $this->price($market);
        $winningPrice = $finalPrices[$market->outcome];
        
        // Market accuracy is how close the final price was to 1.0 for the winning outcome
        return round($winningPrice * 100, 2);
    }

    /**
     * Calculate how much of the available liquidity was utilized.
     */
    private function calculateLiquidityUtilization(Market $market): float
    {
        $stats = $this->getMarketStats($market);
        $totalVolume = $stats['total_volume'];
        $initialLiquidity = (float) $market->liquidity;
        
        return $initialLiquidity > 0 ? round(($totalVolume / $initialLiquidity) * 100, 2) : 0;
    }

    /**
     * Get settlement history for a user.
     */
    public function getUserSettlementHistory(int $userId, int $limit = 10): array
    {
        $resolvedPositions = Position::whereHas('market', function ($query) {
                $query->where('resolved', true);
            })
            ->where('user_id', $userId)
            ->with(['market'])
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();

        $history = [];
        foreach ($resolvedPositions as $position) {
            $market = $position->market;
            $isWinner = $position->choice === $market->outcome;
            $payout = $isWinner ? (float) $position->shares : 0;
            $profit = $payout - (float) $position->cost;

            $history[] = [
                'market_id' => $market->id,
                'market_title' => $market->title,
                'choice' => $position->choice,
                'outcome' => $market->outcome,
                'is_winner' => $isWinner,
                'shares' => (float) $position->shares,
                'cost' => (float) $position->cost,
                'payout' => $payout,
                'profit' => $profit,
                'return_rate' => $position->cost > 0 ? ($profit / (float) $position->cost) * 100 : 0,
                'settled_at' => $market->updated_at,
            ];
        }

        return $history;
    }

    /**
     * Get overall user trading statistics.
     */
    public function getUserTradingStats(int $userId): array
    {
        $allPositions = Position::where('user_id', $userId)->with('market')->get();
        $resolvedPositions = $allPositions->filter(fn($p) => $p->market->resolved);
        
        $winningPositions = $resolvedPositions->filter(fn($p) => $p->choice === $p->market->outcome);
        $losingPositions = $resolvedPositions->filter(fn($p) => $p->choice !== $p->market->outcome);
        
        $totalInvested = $resolvedPositions->sum('cost');
        $totalPayout = $winningPositions->sum('shares'); // €1 per winning share
        $totalProfit = $totalPayout - $totalInvested;
        
        return [
            'total_markets_traded' => $allPositions->pluck('market_id')->unique()->count(),
            'total_positions' => $allPositions->count(),
            'resolved_positions' => $resolvedPositions->count(),
            'winning_positions' => $winningPositions->count(),
            'losing_positions' => $losingPositions->count(),
            'win_rate' => $resolvedPositions->count() > 0 ? 
                round(($winningPositions->count() / $resolvedPositions->count()) * 100, 2) : 0,
            'total_invested' => (float) $totalInvested,
            'total_payout' => (float) $totalPayout,
            'total_profit' => (float) $totalProfit,
            'roi' => $totalInvested > 0 ? round(($totalProfit / $totalInvested) * 100, 2) : 0,
            'average_position_size' => $allPositions->count() > 0 ? 
                round($allPositions->avg('cost'), 2) : 0,
        ];
    }
}