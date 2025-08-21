@props(['market', 'choice' => null, 'userBalance' => 0])

@php
    $mm = app(\App\Services\MarketMaker::class);
    $stats = $mm->getMarketStats($market);
    $prices = $mm->price($market);
    $hasChoices = $market->choices()->exists();
    $choices = $hasChoices ? $market->choices : collect();
@endphp

<div x-data="buyModal({{ json_encode([
    'id' => $market->id,
    'has_choices' => $hasChoices,
    'choices' => $choices->map(fn($c) => ['slug' => $c->slug, 'name' => $c->name, 'party' => $c->party ?? 'Independent'])
]) }}, {{ json_encode($stats) }}, {{ json_encode($prices) }}, {{ $userBalance }})" class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
    <!-- Header -->
    <div class="p-6 border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3 mb-4">
            @if($market->choices()->exists())
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            @else
                <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-lg">?</span>
                </div>
            @endif
            <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Trade on Market</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $market->title }}</p>
            </div>
        </div>

        <!-- Buy/Sell Tabs -->
        <div class="flex bg-slate-100 dark:bg-slate-700 rounded-lg p-1">
            <button @click="tradeType = 'buy'" :class="tradeType === 'buy' ? 'bg-white dark:bg-slate-600 text-slate-900 dark:text-white shadow-sm' : 'text-slate-600 dark:text-slate-300'" class="flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors">
                Buy
            </button>
            <button @click="tradeType = 'sell'" :class="tradeType === 'sell' ? 'bg-white dark:bg-slate-600 text-slate-900 dark:text-white shadow-sm' : 'text-slate-600 dark:text-slate-300'" class="flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors">
                Sell
            </button>
        </div>

        <!-- Market Dropdown for multi-choice -->
        <div class="mt-4 relative" x-show="market.has_choices">
            <button @click="showMarketDropdown = !showMarketDropdown" class="w-full flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600">
                <span class="text-sm font-medium text-slate-900 dark:text-white">Market</span>
                <svg class="w-4 h-4 text-slate-500" :class="showMarketDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Outcome Selection for multi-choice markets -->
        <div x-show="market.has_choices" class="mb-6">
            <div class="grid gap-3">
                <template x-for="choice in market.choices" :key="choice.slug">
                    <div @click="selectedChoice = choice.slug; updateEstimates()" :class="selectedChoice === choice.slug ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-slate-50 dark:hover:bg-slate-700'" class="p-4 border border-slate-200 dark:border-slate-600 rounded-lg cursor-pointer transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-600 dark:to-slate-700 flex items-center justify-center">
                                    <span class="text-slate-600 dark:text-slate-300 font-semibold" x-text="choice.name.charAt(0)"></span>
                                </div>
                                <div>
                                    <div class="font-medium text-slate-900 dark:text-white" x-text="choice.name"></div>
                                    <div class="text-sm text-slate-500 dark:text-slate-400" x-text="choice.party"></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-blue-600" x-text="Math.round((prices[choice.slug] || 0.5) * 100) + '%'"></div>
                                <div class="text-sm text-slate-500" x-text="Math.max(1, Math.round((prices[choice.slug] || 0.5) * 100)) + '¢'"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        
        <!-- Binary market buttons -->
        <div x-show="!market.has_choices" class="mb-6">
            <div class="grid grid-cols-2 gap-3">
                <button @click="selectedChoice = 'yes'; updateEstimates()" :class="selectedChoice === 'yes' ? 'ring-2 ring-green-500 bg-green-50 dark:bg-green-900/20' : 'hover:bg-slate-50 dark:hover:bg-slate-700'" class="p-4 border border-slate-200 dark:border-slate-600 rounded-lg transition-all">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 mb-1" x-text="Math.round((prices.yes || 0.5) * 100) + '%'"></div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">Yes</div>
                    </div>
                </button>
                <button @click="selectedChoice = 'no'; updateEstimates()" :class="selectedChoice === 'no' ? 'ring-2 ring-red-500 bg-red-50 dark:bg-red-900/20' : 'hover:bg-slate-50 dark:hover:bg-slate-700'" class="p-4 border border-slate-200 dark:border-slate-600 rounded-lg transition-all">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600 mb-1" x-text="Math.round((prices.no || 0.5) * 100) + '%'"></div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">No</div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Amount Section -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-3">
                <label class="text-sm font-medium text-slate-900 dark:text-white">Amount</label>
                <div class="text-3xl font-bold text-slate-600 dark:text-slate-400">$<span x-text="amount"></span></div>
            </div>
            
            <!-- Quick amount buttons -->
            <div class="grid grid-cols-4 gap-2 mb-4">
                <button @click="amount = 1; updateEstimates()" class="px-3 py-2 text-sm font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    +$1
                </button>
                <button @click="amount = 20; updateEstimates()" class="px-3 py-2 text-sm font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    +$20
                </button>
                <button @click="amount = 100; updateEstimates()" class="px-3 py-2 text-sm font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    +$100
                </button>
                <button @click="amount = Math.floor(userBalance); updateEstimates()" class="px-3 py-2 text-sm font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    Max
                </button>
            </div>

            <!-- Custom input -->
            <input 
                x-model="amount" 
                @input="updateEstimates()"
                type="number" 
                min="1" 
                :max="userBalance"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Enter amount"
            >
        </div>

        <!-- Estimates -->
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-4 mb-6">
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Shares</span>
                    <span class="font-medium text-slate-900 dark:text-white" x-text="estimatedShares"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Average price</span>
                    <span class="font-medium text-slate-900 dark:text-white" x-text="'$' + avgPrice"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Potential return</span>
                    <span class="font-medium" :class="potentialReturn >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'" x-text="'$' + potentialReturn + ' (' + returnPct + '%)'"></span>
                </div>
            </div>
        </div>

        <!-- Balance info -->
        <div class="flex justify-between items-center text-sm text-slate-600 dark:text-slate-400 mb-6">
            <span>Balance</span>
            <span x-text="'$' + userBalance.toFixed(2)"></span>
        </div>

        <!-- Trade Button -->
        <form method="POST" action="{{ route('markets.trade', $market) }}">
            @csrf
            <input type="hidden" name="choice" :value="selectedChoice">
            <input type="hidden" name="shares" :value="estimatedShares">
            <button 
                type="submit"
                :disabled="!canTrade"
                :class="canTrade ? 'bg-blue-600 hover:bg-blue-700' : 'bg-slate-400 cursor-not-allowed'"
                class="w-full py-3 px-4 rounded-lg text-white font-semibold transition-colors"
                x-text="tradeType === 'buy' ? 'Trade' : 'Sell'"
            >
            </button>
        </form>

        <!-- Terms -->
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-4 text-center">
            By trading, you agree to the <a href="#" class="underline hover:no-underline">Terms of Use</a>.
        </p>
    </div>
</div>

<script>
function buyModal(market, stats, prices, balance) {
    return {
        market: market,
        stats: stats,
        prices: prices,
        userBalance: balance,
        tradeType: 'buy',
        selectedChoice: market.has_choices && market.choices.length > 0 ? market.choices[0].slug : 'yes',
        amount: 0,
        estimatedShares: 0,
        avgPrice: 0,
        potentialReturn: 0,
        returnPct: 0,
        showMarketDropdown: false,

        get canTrade() {
            return this.amount > 0 && this.amount <= this.userBalance && this.selectedChoice;
        },

        updateEstimates() {
            if (!this.amount || !this.selectedChoice || this.amount <= 0) {
                this.estimatedShares = 0;
                this.avgPrice = 0;
                this.potentialReturn = 0;
                this.returnPct = 0;
                return;
            }

            // In prediction markets, we want to buy shares for a certain amount
            // We need to estimate how many shares we can get for the amount we want to spend
            const currentPrice = this.prices[this.selectedChoice] || 0.5;
            
            // Start with a rough estimate of shares we can buy
            // This is simplified - the real LMSR calculation is more complex
            let targetShares = this.amount / currentPrice;
            
            // Round to reasonable precision
            this.estimatedShares = Math.round(targetShares * 100) / 100;
            
            // Estimate the actual cost (this would normally be calculated by the backend)
            const estimatedCost = this.estimatedShares * currentPrice;
            this.avgPrice = this.estimatedShares > 0 ? (estimatedCost / this.estimatedShares).toFixed(4) : '0.0000';
            
            // Potential return is max payout minus cost
            this.potentialReturn = Math.round((this.estimatedShares - estimatedCost) * 100) / 100;
            this.returnPct = estimatedCost > 0 ? Math.round((this.potentialReturn / estimatedCost) * 100) : 0;
        },

        init() {
            this.updateEstimates();
        }
    }
}
</script>
