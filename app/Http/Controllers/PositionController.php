<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PositionController extends Controller
{
    /**
     * Store a new position.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'market_id' => ['required', 'exists:markets,id'],
            'choice' => ['required', Rule::in(['yes', 'no'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $market = Market::findOrFail($request->market_id);

        if ($market->isClosed()) {
            return response()->json(['error' => 'Market is closed for trading'], 422);
        }

        /** @var User $user */
        $user = Auth::user();
        $amount = (float) $request->amount;

        if ($user->balance < $amount) {
            return response()->json(['error' => 'Insufficient balance'], 422);
        }

        try {
            DB::transaction(function () use ($user, $market, $request, $amount) {
                $choice = $request->choice;
                
                // Simplified market maker calculation
                // In a real implementation, you'd use more sophisticated pricing
                $shares = $amount; // Simplified 1:1 for now
                
                // Create position
                Position::create([
                    'user_id' => $user->id,
                    'market_id' => $market->id,
                    'choice' => $choice,
                    'shares' => $shares,
                    'cost' => $amount,
                ]);

                // Update user balance
                $user->balance = bcadd((string) $user->balance, '-' . (string) $amount, 2);
                $user->save();

                // Update market liquidity
                $market->increment('liquidity', $amount);
            });

            return response()->json([
                'success' => 'Position created successfully',
                'user_balance' => $user->balance
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create position'], 500);
        }
    }
}
