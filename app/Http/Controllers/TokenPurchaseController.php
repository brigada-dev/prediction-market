<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\TokenPurchase;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TokenPurchaseController extends Controller
{
    /**
     * Display the token purchase page.
     */
    public function index(): View
    {
        return view('tokens.purchase');
    }

    /**
     * Create a new token purchase intent.
     */
    public function createPurchaseIntent(Request $request): JsonResponse
    {
        $request->validate([
            'token_amount' => ['required', 'integer', 'min:100', 'max:100000'],
            'payment_method' => ['required', 'string', 'in:stripe,coinbase'],
        ]);

        $user = Auth::user();
        $tokenAmount = $request->integer('token_amount');
        $paymentMethod = $request->string('payment_method');

        // Calculate price (example: 1000 tokens = $10 USD)
        $priceUsd = $tokenAmount / 100; // 100 tokens = $1 USD

        try {
            if ($paymentMethod === 'stripe') {
                return $this->createStripeIntent($user, $tokenAmount, $priceUsd);
            } elseif ($paymentMethod === 'coinbase') {
                return $this->createCoinbaseCharge($user, $tokenAmount, $priceUsd);
            }

            return response()->json(['error' => 'Invalid payment method'], 400);
        } catch (\Exception $e) {
            Log::error('Token purchase intent creation failed', [
                'user_id' => $user->id,
                'token_amount' => $tokenAmount,
                'payment_method' => $paymentMethod,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Failed to create purchase intent'], 500);
        }
    }

    /**
     * Handle successful payment completion.
     */
    public function completePurchase(Request $request): JsonResponse
    {
        $request->validate([
            'payment_id' => ['required', 'string'],
            'payment_provider' => ['required', 'string', 'in:stripe,coinbase'],
        ]);

        try {
            $purchase = TokenPurchase::where('payment_id', $request->string('payment_id'))
                ->where('payment_provider', $request->string('payment_provider'))
                ->where('status', 'pending')
                ->first();

            if (!$purchase) {
                return response()->json(['error' => 'Purchase not found'], 404);
            }

            DB::transaction(function () use ($purchase) {
                // Add tokens to user balance
                $user = $purchase->user;
                $user->increment('balance', $purchase->tokens_purchased);

                // Mark purchase as completed
                $purchase->markAsCompleted();
            });

            return response()->json([
                'success' => true,
                'message' => 'Tokens added to your account successfully!',
                'tokens_purchased' => $purchase->tokens_purchased,
                'new_balance' => $purchase->user->fresh()->balance,
            ]);
        } catch (\Exception $e) {
            Log::error('Token purchase completion failed', [
                'payment_id' => $request->string('payment_id'),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Failed to complete purchase'], 500);
        }
    }

    /**
     * Create Stripe payment intent (placeholder implementation).
     */
    private function createStripeIntent(User $user, int $tokenAmount, float $priceUsd): JsonResponse
    {
        // This is a placeholder - in production, you would integrate with Stripe SDK
        $paymentId = 'pi_' . uniqid();

        $purchase = TokenPurchase::create([
            'user_id' => $user->id,
            'payment_provider' => 'stripe',
            'payment_id' => $paymentId,
            'tokens_purchased' => $tokenAmount,
            'amount_paid' => $priceUsd,
            'currency' => 'USD',
            'status' => 'pending',
            'payment_data' => [
                'client_secret' => $paymentId . '_secret_' . uniqid(),
            ],
        ]);

        return response()->json([
            'success' => true,
            'payment_provider' => 'stripe',
            'payment_id' => $paymentId,
            'client_secret' => $purchase->payment_data['client_secret'],
            'amount' => $priceUsd,
            'currency' => 'USD',
        ]);
    }

    /**
     * Create Coinbase Commerce charge (placeholder implementation).
     */
    private function createCoinbaseCharge(User $user, int $tokenAmount, float $priceUsd): JsonResponse
    {
        // This is a placeholder - in production, you would integrate with Coinbase Commerce SDK
        $chargeId = 'cb_' . uniqid();

        $purchase = TokenPurchase::create([
            'user_id' => $user->id,
            'payment_provider' => 'coinbase',
            'payment_id' => $chargeId,
            'tokens_purchased' => $tokenAmount,
            'amount_paid' => $priceUsd,
            'currency' => 'USD',
            'status' => 'pending',
            'payment_data' => [
                'hosted_url' => 'https://commerce.coinbase.com/charges/' . $chargeId,
            ],
        ]);

        return response()->json([
            'success' => true,
            'payment_provider' => 'coinbase',
            'payment_id' => $chargeId,
            'hosted_url' => $purchase->payment_data['hosted_url'],
            'amount' => $priceUsd,
            'currency' => 'USD',
        ]);
    }
}
