<x-app-layout>
    <div class="bg-gray-50 dark:bg-gray-900 min-h-screen transition-theme">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 transition-theme">
                            Tregjet e Parashikimeve
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 transition-theme">
                            Tregtoni mbi ngjarjet e ardhshme dhe fitoni para reale nga parashikimet tuaja
                        </p>
                    </div>
                    
                    @auth
                        <div class="mt-4 sm:mt-0">
                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-6 py-3 rounded-xl shadow-sm transition-theme">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Bilanci Juaj</span>
                                <div class="font-bold text-2xl text-gray-900 dark:text-white transition-theme">
                                    €{{ number_format(auth()->user()->balance, 2) }}
                                </div>
                            </div>
                        </div>
                    @endauth
                </div>
                
                <!-- Categories/Filters -->
                <div class="flex space-x-2 overflow-x-auto pb-2">
                    <button class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg font-medium text-sm whitespace-nowrap transition-theme">
                        Të Gjitha
                    </button>
                    <button class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-lg font-medium text-sm whitespace-nowrap hover:bg-gray-50 dark:hover:bg-gray-700 transition-theme">
                        Politikë
                    </button>
                    <button class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-lg font-medium text-sm whitespace-nowrap hover:bg-gray-50 dark:hover:bg-gray-700 transition-theme">
                        Ekonomi
                    </button>
                    <button class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-lg font-medium text-sm whitespace-nowrap hover:bg-gray-50 dark:hover:bg-gray-700 transition-theme">
                        Sport
                    </button>
                    <button class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-lg font-medium text-sm whitespace-nowrap hover:bg-gray-50 dark:hover:bg-gray-700 transition-theme">
                        Teknologji
                    </button>
                </div>
            </div>

            <!-- Markets Grid -->
            <div class="space-y-4">
                @forelse($markets as $market)
                    @php
                        $marketMaker = app(\App\Services\MarketMaker::class);
                        $stats = $marketMaker->getMarketStats($market);
                    @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-xl transition-all duration-200 hover:border-gray-300 dark:hover:border-gray-600">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    @if($market->resolved)
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300 transition-theme">
                                            ✓ I Zgjidhur
                                        </span>
                                    @elseif($market->isClosed())
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 transition-theme">
                                            I Mbyllur
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 transition-theme">
                                            🔥 Live
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-500 dark:text-gray-400 transition-theme">{{ $market->closes_at->format('M j') }}</span>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 transition-theme">
                                    {{ $market->title }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2 mb-4 transition-theme">
                                    {{ $market->description }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl p-4 transition-theme">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-green-800 dark:text-green-300">PO</span>
                                    <span class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $stats['probability_yes'] }}€</span>
                                </div>
                                <div class="text-xs text-green-600 dark:text-green-400 mt-1">+{{ rand(1, 5) }}€ sot</div>
                            </div>
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-4 transition-theme">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-red-800 dark:text-red-300">JO</span>
                                    <span class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $stats['probability_no'] }}€</span>
                                </div>
                                <div class="text-xs text-red-600 dark:text-red-400 mt-1">-{{ rand(1, 3) }}€ sot</div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-4">
                            <div class="flex space-x-4 text-sm text-gray-500 dark:text-gray-400 transition-theme">
                                <span>{{ number_format($stats['total_volume'], 0) }} €</span>
                                <span>•</span>
                                <span>{{ $stats['total_positions'] }} tregtarë</span>
                            </div>
                            @if($market->resolved)
                                <div class="text-sm font-semibold text-green-600 dark:text-green-400 transition-theme">
                                    Fitues: {{ strtoupper($market->outcome === 'yes' ? 'PO' : 'JO') }}
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('markets.show', $market) }}" 
                           class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-500 dark:to-purple-500 text-white text-center py-3 rounded-xl font-semibold hover:opacity-90 transition-opacity">
                            @if($market->resolved)
                                Shikoni Rezultatet
                            @elseif($market->isClosed())
                                Tregu i Mbyllur
                            @else
                                Tregtoni Tash
                            @endif
                        </a>
                    </div>
                @empty
                    <div class="text-center py-16">
                        <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 transition-theme">
                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 transition-theme">Asnjë treg i disponueshëm</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 transition-theme">Tregjet e parashikimeve do të jenë të disponueshme së shpejti.</p>
                        <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 dark:bg-blue-500 text-white rounded-lg font-medium hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                            Kthehuni te Fillimi
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($markets->hasPages())
                <div class="mt-8">
                    {{ $markets->links() }}
                </div>
            @endif

            <!-- Info Section -->
            <div class="mt-12 bg-gray-50 dark:bg-gray-800 rounded-lg p-6 transition-theme">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 transition-theme">Si Funksionojnë Tregjti të Parashikimeve</h3>
                <div class="grid md:grid-cols-3 gap-6 text-sm">
                    <div>
                        <div class="text-blue-600 dark:text-blue-400 font-semibold mb-2 transition-theme">📈 Bëni Parashikime</div>
                        <p class="text-gray-600 dark:text-gray-400 transition-theme">Blini aksione në rezultate që besoni se do të ndodhin. Çmimet reflektojnë probabilitetin e çdo rezultati.</p>
                    </div>
                    <div>
                        <div class="text-green-600 dark:text-green-400 font-semibold mb-2 transition-theme">🎯 Parashikimet e Sakta Fitojnë</div>
                        <p class="text-gray-600 dark:text-gray-400 transition-theme">Kur tregjti zgjidhen, aksionet fitüese paguajnë euro. Sa më të sakta parashikimet tuaja, aq më shumë fitoni.</p>
                    </div>
                    <div>
                        <div class="text-purple-600 dark:text-purple-400 font-semibold mb-2 transition-theme">🏆 Shkembeni Shpërblime</div>
                        <p class="text-gray-600 dark:text-gray-400 transition-theme">Përdorni eurot e fituara për të marra çmima nga dyqani ynë ose konkurroni në tregjti të reja.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>