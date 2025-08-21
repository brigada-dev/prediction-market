<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\Position;
use App\Models\User;
use App\Services\MarketMaker;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MarketController extends Controller
{
    /**
     * Display a listing of markets.
     */
    public function index(): View
    {
        $markets = Market::with(['positions'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('markets.index', compact('markets'));
    }

    /**
     * Display the specified market.
     */
    public function show(Market $market, MarketMaker $marketMaker): View
    {
        $market->load(['positions.user']);

        // Get market statistics from MarketMaker
        $stats = $marketMaker->getMarketStats($market);
        
        // Get historical price data for chart
        $historicalData = $marketMaker->getHistoricalPrices($market, 24);

        // Get user's positions if authenticated
        $userPositions = Auth::check() 
            ? $market->positions()->where('user_id', Auth::id())->get()
            : collect();

        return view('markets.show', compact('market', 'stats', 'userPositions', 'historicalData'));
    }

    /**
     * Get historical price data for API.
     */
    public function getHistoricalPrices(Request $request, Market $market, MarketMaker $marketMaker): JsonResponse
    {
        $hours = (int) $request->get('hours', 24);
        
        // Validate hours parameter
        $validHours = [1, 6, 24, 168, 720]; // 1h, 6h, 1d, 1w, 1m
        if (!in_array($hours, $validHours)) {
            $hours = 24; // Default to 24 hours
        }
        
        $historicalData = $marketMaker->getHistoricalPrices($market, $hours);
        
        return response()->json($historicalData);
    }

    /**
     * Handle trading on a market.
     */
    public function trade(Request $request, Market $market, MarketMaker $marketMaker): JsonResponse|RedirectResponse
    {
        $validChoices = $market->choices()->exists()
            ? $market->choices->pluck('slug')->all()
            : ['yes', 'no'];

        $request->validate([
            'choice' => ['required', Rule::in($validChoices)],
            'shares' => ['required', 'numeric', 'min:0.01'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $choice = $request->choice;
        $shares = (float) $request->shares;

        // Validate the trade using MarketMaker
        $validation = $marketMaker->validateTrade($market, $choice, $shares, (float) $user->balance);

        if (!$validation['valid']) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Trade validation failed',
                    'errors' => $validation['errors']
                ], 422);
            }
            
            return redirect()->back()->withErrors(['trade' => implode(' ', $validation['errors'])]);
        }

        $cost = $validation['cost'];

        try {
            DB::transaction(function () use ($user, $market, $choice, $shares, $cost, $marketMaker) {
                // Create position with LMSR calculated cost
                $payload = [
                    'user_id' => $user->id,
                    'market_id' => $market->id,
                    'shares' => $shares,
                    'cost' => $cost,
                ];
                
                if ($market->choices()->exists()) {
                    $choiceModel = $market->choices->firstWhere('slug', $choice);
                    $payload['choice_id'] = $choiceModel?->id;
                } else {
                    $payload['choice'] = $choice;
                }
                Position::create($payload);

                // Update user balance
                $newBalance = bcadd((string) $user->balance, '-' . (string) $cost, 2);
                $user->setAttribute('balance', $newBalance);
                $user->save();

                // Clear market cache
                $marketMaker->clearMarketCache($market);
            });

            // Get choice name for display
            $choiceName = $choice;
            if ($market->choices()->exists()) {
                $choiceModel = $market->choices->firstWhere('slug', $choice);
                $choiceName = $choiceModel?->name ?? $choice;
            } else {
                $choiceName = strtoupper($choice);
            }

            if ($request->expectsJson()) {
                $stats = $marketMaker->getMarketStats($market);
                return response()->json([
                    'success' => 'Trade executed successfully',
                    'cost' => $cost,
                    'shares' => $shares,
                    'choice' => $choice,
                    'user_balance' => $user->balance,
                    'market_stats' => $stats
                ]);
            }

            return redirect()->back()->with('success', "Trade executed successfully! You bought {$shares} {$choiceName} shares for \${$cost}.");

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Trade execution failed: ' . $e->getMessage()], 500);
            }
            
            return redirect()->back()->withErrors(['trade' => 'Trade execution failed. Please try again.']);
        }
    }

    /**
     * Resolve a market.
     */
    public function resolve(Request $request, Market $market): JsonResponse|RedirectResponse
    {
        $request->validate([
            'outcome' => ['required', Rule::in(['yes', 'no'])],
        ]);

        if ($market->resolved) {
            return response()->json(['error' => 'Market already resolved'], 422);
        }

        try {
            DB::transaction(function () use ($market, $request) {
                $outcome = $request->outcome;
                
                // Update market
                $market->update([
                    'resolved' => true,
                    'outcome' => $outcome,
                ]);

                // Pay out winning positions
                $winningPositions = $market->positions()->where('choice', $outcome)->get();
                
                foreach ($winningPositions as $position) {
                    // Simplified payout: return cost + profit based on shares
                    $payout = $position->cost + $position->shares;
                    $position->user->increment('balance', $payout);
                }
            });

            return response()->json(['success' => 'Market resolved successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Resolution failed'], 500);
        }
    }
}
