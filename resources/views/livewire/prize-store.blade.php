<div class="space-y-8">
    <!-- Header -->
    <div class="text-center">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Dyqani i Çmimeve</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Shkembeni monedhat tuaja për çmima të jashtëzakonshme!
        </p>
        @auth
            <div class="mt-4 inline-block bg-blue-50 dark:bg-blue-900/20 px-4 py-2 rounded-lg">
                <span class="text-sm text-gray-600 dark:text-gray-400">Bilanci Juaj: </span>
                <span class="font-bold text-blue-600 dark:text-blue-400">
                    {{ number_format(auth()->user()->balance) }} monedha
                </span>
            </div>
        @endauth
    </div>

    <!-- Messages -->
    @if (session()->has('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Prize Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($prizes as $prize)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <!-- Prize Image/Icon -->
                <div class="h-48 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                    <span class="text-6xl">{{ $prize['image'] }}</span>
                </div>
                
                <!-- Prize Details -->
                <div class="p-6">
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">
                        {{ $prize['name'] }}
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        {{ $prize['description'] }}
                    </p>
                    
                    <!-- Cost and Button -->
                    <div class="flex items-center justify-between">
                        <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
                            {{ number_format($prize['cost']) }} tokens
                        </div>
                        
                        @auth
                            @if(auth()->user()->balance >= $prize['cost'])
                                <button wire:click="redeem('{{ $prize['name'] }}', '{{ $prize['description'] }}', {{ $prize['cost'] }})"
                                        wire:loading.attr="disabled"
                                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-4 py-2 rounded-md font-medium transition-colors">
                                    <span wire:loading.remove wire:target="redeem">Redeem</span>
                                    <span wire:loading wire:target="redeem">Processing...</span>
                                </button>
                            @else
                                <button disabled class="bg-gray-300 text-gray-500 px-4 py-2 rounded-md font-medium cursor-not-allowed">
                                    Not Enough Tokens
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium transition-colors">
                                Login to Redeem
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Recent Claims (for authenticated users) -->
    @auth
        @if($userClaims->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                    Your Recent Claims
                </h3>
                
                <div class="space-y-3">
                    @foreach($userClaims as $claim)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $claim->prize_name }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $claim->created_at->format('M j, Y g:i A') }}
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ number_format($claim->token_cost) }} tokens
                                </div>
                                <div class="text-xs">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $claim->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $claim->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $claim->status === 'shipped' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $claim->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($claim->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 text-center">
                    <a href="{{ route('profile.show') }}" class="text-blue-600 hover:text-blue-500 font-medium">
                        View All Claims →
                    </a>
                </div>
            </div>
        @endif
    @endauth

    <!-- How it Works -->
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">How It Works</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
            <div>
                <div class="text-3xl mb-2">📈</div>
                <div class="font-medium text-gray-900 dark:text-white">Trade on Markets</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Make predictions and earn tokens</div>
            </div>
            <div>
                <div class="text-3xl mb-2">🎁</div>
                <div class="font-medium text-gray-900 dark:text-white">Choose Prizes</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Browse our selection of rewards</div>
            </div>
            <div>
                <div class="text-3xl mb-2">🚚</div>
                <div class="font-medium text-gray-900 dark:text-white">Get Delivered</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">We'll ship it to your door</div>
            </div>
        </div>
    </div>
</div>