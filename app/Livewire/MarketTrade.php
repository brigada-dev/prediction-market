<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Market;
use App\Models\Position;
use App\Services\MarketMaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Validate;

class MarketTrade extends Component
{
    public Market $market;
    
    #[Validate('required|in:yes,no')]
    public string $choice = 'yes';
    
    #[Validate('required|numeric|min:0.01|max:1000')]
    public float $shares = 1.0;
    
    public array $stats = [];
    public float $estimatedCost = 0.0;
    public ?string $errorMessage = null;

    public function mount(Market $market): void
    {
        $this->market = $market;
        $this->updateStats();
        $this->updateEstimatedCost();
    }

    public function updatedShares(): void
    {
        $this->updateEstimatedCost();
    }

    public function updatedChoice(): void
    {
        $this->updateEstimatedCost();
    }

    public function updateStats(): void
    {
        $marketMaker = app(MarketMaker::class);
        $this->stats = $marketMaker->getMarketStats($this->market);
    }

    public function updateEstimatedCost(): void
    {
        $marketMaker = app(MarketMaker::class);
        
        try {
            $this->estimatedCost = $marketMaker->costToBuy($this->market, $this->choice, $this->shares);
            $this->errorMessage = null;
        } catch (\Exception $e) {
            $this->estimatedCost = 0.0;
            $this->errorMessage = 'Unable to calculate cost';
        }
    }

    public function trade(): void
    {
        $this->validate();

        if (!Auth::check()) {
            $this->addError('auth', 'You must be logged in to trade.');
            return;
        }

        $user = Auth::user();
        $marketMaker = app(MarketMaker::class);

        // Validate trade
        $validation = $marketMaker->validateTrade(
            $this->market, 
            $this->choice, 
            $this->shares, 
            (float) $user->balance
        );

        if (!$validation['valid']) {
            foreach ($validation['errors'] as $error) {
                $this->addError('trade', $error);
            }
            return;
        }

        try {
            DB::transaction(function () use ($user, $marketMaker, $validation) {
                $cost = $validation['cost'];

                // Create position
                Position::create([
                    'user_id' => $user->id,
                    'market_id' => $this->market->id,
                    'choice' => $this->choice,
                    'shares' => $this->shares,
                    'cost' => $cost,
                ]);

                // Update user balance
                $user->balance = bcadd((string) $user->balance, '-' . (string) $cost, 2);
                $user->save();

                // Clear market cache
                $marketMaker->clearMarketCache($this->market);
            });

            // Update stats and reset form
            $this->updateStats();
            $this->updateEstimatedCost();
            $this->shares = 1.0;

            session()->flash('success', "Trade executed successfully! You bought {$this->shares} {$this->choice} shares for \${$validation['cost']}.");

            // Dispatch browser event to update balance in navbar
            $this->dispatch('balance-updated', balance: Auth::user()->balance);

        } catch (\Exception $e) {
            $this->addError('trade', 'Trade execution failed. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.market-trade');
    }
}
