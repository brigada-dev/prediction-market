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
use Illuminate\Validation\Rule;

class MarketTrade extends Component
{
    public Market $market;
    
    public string $choice = 'yes';
    
    #[Validate('required|numeric|min:0.01|max:1000')]
    public float $shares = 1.0;
    
    public array $stats = [];
    public float $estimatedCost = 0.0;
    public ?string $errorMessage = null;
    public float $estimatedPayout = 0.0;
    public float $estimatedProfitIfWin = 0.0;
    public float $expectedValue = 0.0;
    public float $breakEvenProbability = 0.0; // 0..1
    public float $pricePerShare = 0.0;
    public float $returnIfWinPct = 0.0;

    public function mount(Market $market): void
    {
        $this->market = $market;
        // Default choice based on market type
        if ($this->market->choices()->exists()) {
            $first = $this->market->choices()->first();
            $this->choice = $first?->slug ?? '';
        } else {
            $this->choice = 'yes';
        }
        $this->updateStats();
        $this->updateEstimatedCost();
    }

    /**
     * Dynamic validation rules to support multi-outcome markets.
     */
    public function rules(): array
    {
        $validChoices = $this->market->choices()->exists()
            ? $this->market->choices->pluck('slug')->all()
            : ['yes', 'no'];

        return [
            'choice' => ['required', Rule::in($validChoices)],
            'shares' => ['required', 'numeric', 'min:0.01', 'max:1000'],
        ];
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

            // Derived metrics
            $prices = $marketMaker->price($this->market);
            if ($this->market->choices()->exists()) {
                $probability = (float) ($prices[$this->choice] ?? 0.5);
            } else {
                $probability = $this->choice === 'yes' ? ($prices['yes'] ?? 0.5) : ($prices['no'] ?? 0.5);
            }

            $this->estimatedPayout = round(max(0, $this->shares), 2);
            $this->pricePerShare = $this->shares > 0 ? round($this->estimatedCost / $this->shares, 4) : 0.0;
            $this->estimatedProfitIfWin = round($this->estimatedPayout - $this->estimatedCost, 2);
            $this->breakEvenProbability = $this->shares > 0 ? min(1.0, max(0.0, $this->estimatedCost / $this->shares)) : 0.0;
            $this->expectedValue = round(($this->shares * $probability) - $this->estimatedCost, 2);
            $this->returnIfWinPct = $this->estimatedCost > 0 ? round(($this->estimatedProfitIfWin / $this->estimatedCost) * 100, 1) : 0.0;
        } catch (\Exception $e) {
            $this->estimatedCost = 0.0;
            $this->errorMessage = 'Unable to calculate cost';
            $this->estimatedPayout = 0.0;
            $this->estimatedProfitIfWin = 0.0;
            $this->expectedValue = 0.0;
            $this->breakEvenProbability = 0.0;
            $this->pricePerShare = 0.0;
            $this->returnIfWinPct = 0.0;
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
                $payload = [
                    'user_id' => $user->id,
                    'market_id' => $this->market->id,
                    'shares' => $this->shares,
                    'cost' => $cost,
                ];
                if ($this->market->choices()->exists()) {
                    $choiceModel = $this->market->choices->firstWhere('slug', $this->choice);
                    $payload['choice_id'] = $choiceModel?->id;
                } else {
                    $payload['choice'] = $this->choice;
                }
                Position::create($payload);

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

            $label = $this->market->choices()->exists()
                ? ($this->market->choices->firstWhere('slug', $this->choice)?->name ?? $this->choice)
                : strtoupper($this->choice);

            session()->flash('success', "Trade executed successfully! You bought {$this->shares} {$label} shares for €{$validation['cost']}.");

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
