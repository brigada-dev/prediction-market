<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\PrizeClaim;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PrizeStore extends Component
{
    public array $prizes = [
        [
            'name' => 'Premium T-Shirt',
            'description' => 'High-quality cotton t-shirt with prediction market logo',
            'cost' => 1000,
            'image' => '🎽'
        ],
        [
            'name' => 'Coffee Mug',
            'description' => 'Ceramic mug perfect for your morning coffee',
            'cost' => 500,
            'image' => '☕'
        ],
        [
            'name' => 'Sticker Pack',
            'description' => 'Collection of vinyl stickers for your laptop',
            'cost' => 200,
            'image' => '🎫'
        ],
        [
            'name' => 'Prediction Market Hoodie',
            'description' => 'Comfortable hoodie for prediction market enthusiasts',
            'cost' => 2000,
            'image' => '👕'
        ],
        [
            'name' => 'Wireless Mouse Pad',
            'description' => 'Premium mouse pad with wireless charging capability',
            'cost' => 1500,
            'image' => '🖱️'
        ],
        [
            'name' => 'Book Voucher (€25)',
            'description' => 'Amazon gift card for purchasing books',
            'cost' => 2500,
            'image' => '📚'
        ],
    ];

    public function redeem(string $prizeName, string $prizeDescription, int $tokenCost): void
    {
        if (!Auth::check()) {
            session()->flash('error', 'You must be logged in to redeem prizes.');
            return;
        }

        $user = Auth::user();

        if ($user->balance < $tokenCost) {
            session()->flash('error', 'Not enough tokens to redeem this prize. You need ' . number_format($tokenCost) . ' tokens but only have ' . number_format($user->balance) . ' tokens.');
            return;
        }

        try {
            // Deduct tokens from user balance
            $user->balance = bcadd((string) $user->balance, '-' . (string) $tokenCost, 2);
            $user->save();

            // Create prize claim
            PrizeClaim::create([
                'user_id' => $user->id,
                'prize_name' => $prizeName,
                'prize_description' => $prizeDescription,
                'token_cost' => $tokenCost,
                'status' => 'pending',
                'claimed_at' => now(),
            ]);

            session()->flash('success', "You successfully redeemed {$prizeName}! Your claim is now pending approval.");

            // Dispatch browser event to update balance
            $this->dispatch('balance-updated', balance: $user->balance);

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while processing your redemption. Please try again.');
        }
    }

    public function getUserClaims()
    {
        if (!Auth::check()) {
            return collect();
        }

        return Auth::user()->prizeClaims()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.prize-store', [
            'userClaims' => $this->getUserClaims(),
        ]);
    }
}
