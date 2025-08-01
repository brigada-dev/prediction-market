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

        $eYes = exp($liquidity['yes'] / $b);
        $eNo = exp($liquidity['no'] / $b);

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

        $qYesBefore = $liquidity['yes'];
        $qNoBefore = $liquidity['no'];

        // Calculate new quantities after purchase
        if ($choice === 'yes') {
            $qYesAfter = $qYesBefore + $shares;
            $qNoAfter = $qNoBefore;
        } else {
            $qYesAfter = $qYesBefore;
            $qNoAfter = $qNoBefore + $shares;
        }

        // LMSR cost function
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
        $liquidity = $this->getLiquidity($market);
        $prices = $this->price($market);
        
        $totalVolume = $liquidity['yes'] + $liquidity['no'];
        $totalPositions = Position::where('market_id', $market->id)->count();

        return [
            'liquidity' => $liquidity,
            'prices' => $prices,
            'total_volume' => round($totalVolume, 2),
            'total_positions' => $totalPositions,
            'probability_yes' => round($prices['yes'] * 100, 1),
            'probability_no' => round($prices['no'] * 100, 1),
        ];
    }

    /**
     * Clear market cache when positions change.
     */
    public function clearMarketCache(Market $market): void
    {
        Cache::forget("market_liquidity_{$market->id}");
    }

    /**
     * Validate if a trade is possible given market constraints.
     */
    public function validateTrade(Market $market, string $choice, float $shares, float $userBalance): array
    {
        $errors = [];

        if (!in_array($choice, ['yes', 'no'])) {
            $errors[] = 'Invalid choice. Must be "yes" or "no".';
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