<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <div class="space-y-6">
        <!-- Market Status -->
        <div class="text-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                Tregtoni mbi: {{ $market->title }}
            </h3>
            
            @if($market->isClosed())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    Tregu ashtë i mbyllur për tregtim
                </div>
            @endif
        </div>

        <!-- Market Statistics -->
        <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">
                    {{ $stats['probability_yes'] ?? 0 }}%
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300">Probabiliteti PO</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600">
                    {{ $stats['probability_no'] ?? 0 }}%
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-300">Probabiliteti JO</div>
            </div>
        </div>

        <!-- Trading Form -->
        @auth
            @if(!$market->isClosed())
                <form wire:submit="trade" class="space-y-4">
                    <!-- Choice Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Zgjidhni Anën
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" 
                                    wire:click="$set('choice', 'yes')"
                                    class="px-4 py-2 text-sm font-medium rounded-md border {{ $choice === 'yes' ? 'bg-green-600 text-white border-green-600' : 'bg-white text-green-600 border-green-300 hover:bg-green-50' }}">
                                PO ({{ $stats['probability_yes'] ?? 0 }}%)
                            </button>
                            <button type="button" 
                                    wire:click="$set('choice', 'no')"
                                    class="px-4 py-2 text-sm font-medium rounded-md border {{ $choice === 'no' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-red-600 border-red-300 hover:bg-red-50' }}">
                                JO ({{ $stats['probability_no'] ?? 0 }}%)
                            </button>
                        </div>
                        @error('choice') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Shares Input -->
                    <div>
                        <label for="shares" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Numri i Aksioneve
                        </label>
                        <input type="number" 
                               wire:model.live="shares" 
                               id="shares"
                               step="0.01" 
                               min="0.01" 
                               max="1000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('shares') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Cost Estimation -->
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Kosto e Vlerësuar:</span>
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                €{{ number_format($estimatedCost, 2) }}
                            </span>
                        </div>
                        
                        @if($errorMessage)
                            <div class="text-red-500 text-sm mt-1">{{ $errorMessage }}</div>
                        @endif

                        @auth
                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Bilanci juaj: €{{ number_format(auth()->user()->balance, 2) }}
                            </div>
                        @endauth
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            wire:loading.attr="disabled"
                            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-medium py-2 px-4 rounded-md transition-colors">
                        <span wire:loading.remove>
                            Ekzekutoni Tregtimin
                        </span>
                        <span wire:loading>
                            Duke përpunu...
                        </span>
                    </button>

                    <!-- Error Messages -->
                    @error('auth') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                    @error('trade') <div class="text-red-500 text-sm">{{ $message }}</div> @enderror
                </form>
            @endif
        @else
            <div class="text-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-yellow-800">
                    <a href="{{ route('login') }}" class="font-medium underline hover:no-underline">
                        Hyni
                    </a> 
                    me fillu me tregtua në kët treg.
                </p>
            </div>
        @endauth

        <!-- Success Message -->
        @if (session()->has('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Additional Market Info -->
        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
            <div>Volumi Total: {{ $stats['total_volume'] ?? 0 }} aksione</div>
            <div>Pozitat Totale: {{ $stats['total_positions'] ?? 0 }}</div>
            <div>Mbyllet: {{ $market->closes_at->format('M j, Y g:i A') }}</div>
        </div>
    </div>
</div>