<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $market->title }}
            </h2>
            
            <div class="flex items-center space-x-4">
                @if($market->resolved)
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                        I Zgjidhur: {{ strtoupper($market->outcome === 'yes' ? 'PO' : ($market->outcome === 'no' ? 'JO' : $market->outcome)) }}
                    </span>
                @elseif($market->isClosed())
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                        Tregu i Mbyllur
                    </span>
                @else
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                        Treg Aktiv
                    </span>
                @endif
                
                <a href="{{ route('markets.index') }}" 
                   class="text-blue-600 hover:text-blue-500 font-medium">
                    ← Kthehuni tek Tregjti
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Market Description -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detajet e Tregut</h3>
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                            {{ $market->description }}
                        </p>
                        
                        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <div class="text-gray-600 dark:text-gray-400">Krijuar</div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $market->created_at->format('M j, Y') }}
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-600 dark:text-gray-400">Mbyllet</div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $market->closes_at->format('M j, Y g:i A') }}
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-600 dark:text-gray-400">Volumi</div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ number_format($stats['total_volume'], 2) }} aksione
                                </div>
                            </div>
                            <div>
                                <div class="text-gray-600 dark:text-gray-400">Pozitat</div>
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $stats['total_positions'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price Chart Placeholder -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Historia e Çmimeve</h3>
                        <div class="h-64 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <div class="text-center text-gray-500 dark:text-gray-400">
                                <div class="text-4xl mb-2">📈</div>
                                <p>Grafiku i çmimeve së shpejti</p>
                                <p class="text-sm">Aktuale PO: {{ $stats['probability_yes'] }}% | JO: {{ $stats['probability_no'] }}%</p>
                            </div>
                        </div>
                    </div>

                    <!-- User Positions -->
                    @auth
                        @if($userPositions->count() > 0)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pozitat Tuaja</h3>
                                <div class="space-y-3">
                                    @foreach($userPositions as $position)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div>
                                                <span class="font-medium {{ $position->choice === 'yes' ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ strtoupper($position->choice) }}
                                                </span>
                                                <span class="text-gray-600 dark:text-gray-400 ml-2">
                                                    {{ number_format($position->shares, 2) }} aksione
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                                    Kosto: ${{ number_format($position->cost, 2) }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-500">
                                                    {{ $position->created_at->format('M j, g:i A') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @php
                                    $totalYesShares = $userPositions->where('choice', 'yes')->sum('shares');
                                    $totalNoShares = $userPositions->where('choice', 'no')->sum('shares');
                                    $totalCost = $userPositions->sum('cost');
                                @endphp
                                
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <div class="text-gray-600 dark:text-gray-400">Aksione PO</div>
                                            <div class="font-medium text-green-600">{{ number_format($totalYesShares, 2) }}</div>
                                        </div>
                                        <div>
                                            <div class="text-gray-600 dark:text-gray-400">Aksione JO</div>
                                            <div class="font-medium text-red-600">{{ number_format($totalNoShares, 2) }}</div>
                                        </div>
                                        <div>
                                            <div class="text-gray-600 dark:text-gray-400">Totali i Investuar</div>
                                            <div class="font-medium text-gray-900 dark:text-white">${{ number_format($totalCost, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>

                <!-- Trading Sidebar -->
                <div class="space-y-6">
                    <!-- Trading Component -->
                    @livewire('market-trade', ['market' => $market])

                    <!-- Market Stats -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statistikat e Tregut</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Probabiliteti PO</span>
                                    <span class="font-medium text-green-600">{{ $stats['probability_yes'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $stats['probability_yes'] }}%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Probabiliteti JO</span>
                                    <span class="font-medium text-red-600">{{ $stats['probability_no'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-red-600 h-2 rounded-full" style="width: {{ $stats['probability_no'] }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Volumi Total</span>
                                <span class="font-medium">{{ number_format($stats['total_volume'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Aksione PO</span>
                                <span class="font-medium text-green-600">{{ number_format($stats['liquidity']['yes'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Aksione JO</span>
                                <span class="font-medium text-red-600">{{ number_format($stats['liquidity']['no'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Pozitat Aktive</span>
                                <span class="font-medium">{{ $stats['total_positions'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- How Trading Works -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Si Funksionon</h4>
                        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                            <p>• Blini aksione PO nëse mendoni se rezultati do të ndodh</p>
                            <p>• Blini aksione JO nëse mendoni se nuk do të ndodh</p>
                            <p>• Çmimet ndryshojnë bazuar në kërkesen e tregut</p>
                            <p>• Aksionet fitüese paguajnë kur tregu zgjidhet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>