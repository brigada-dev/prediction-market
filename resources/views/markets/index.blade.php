<x-app-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            Tregjet e Parashikimeve
                        </h1>
                        <p class="text-gray-600">
                            Tregtoni mbi ngjarjet e ardhshme dhe fitoni para reale nga parashikimet tuaja
                        </p>
                    </div>
                    
                    @auth
                        <div class="mt-4 sm:mt-0">
                            <div class="bg-white border border-gray-200 px-6 py-3 rounded-xl shadow-sm">
                                <span class="text-sm text-gray-500">Bilanci Juaj</span>
                                <div class="font-bold text-2xl text-gray-900">
                                    €{{ number_format(auth()->user()->balance, 2) }}
                                </div>
                            </div>
                        </div>
                    @endauth
                </div>
                
                <!-- Categories/Filters -->
                <div class="flex space-x-2 overflow-x-auto pb-2">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium text-sm whitespace-nowrap">
                        Të Gjitha
                    </button>
                    <button class="px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-lg font-medium text-sm whitespace-nowrap hover:bg-gray-50">
                        Politikë
                    </button>
                    <button class="px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-lg font-medium text-sm whitespace-nowrap hover:bg-gray-50">
                        Ekonomi
                    </button>
                    <button class="px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-lg font-medium text-sm whitespace-nowrap hover:bg-gray-50">
                        Sport
                    </button>
                    <button class="px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-lg font-medium text-sm whitespace-nowrap hover:bg-gray-50">
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
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 hover:shadow-lg transition-all duration-200 hover:border-gray-300">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    @if($market->resolved)
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            ✓ I Zgjidhur
                                        </span>
                                    @elseif($market->isClosed())
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                            I Mbyllur
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-600">
                                            🔥 Live
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-500">{{ $market->closes_at->format('M j') }}</span>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">
                                    {{ $market->title }}
                                </h3>
                                <p class="text-gray-600 text-sm line-clamp-2 mb-4">
                                    {{ $market->description }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-green-800">PO</span>
                                    <span class="text-2xl font-bold text-green-700">{{ $stats['probability_yes'] }}€</span>
                                </div>
                                                                  <div class="text-xs text-green-600 mt-1">+{{ rand(1, 5) }}€ sot</div>
                            </div>
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                                <div class="flex justify-between items-center">
                                    <span class="font-semibold text-red-800">JO</span>
                                    <span class="text-2xl font-bold text-red-700">{{ $stats['probability_no'] }}€</span>
                                </div>
                                                                  <div class="text-xs text-red-600 mt-1">-{{ rand(1, 3) }}€ sot</div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-4">
                            <div class="flex space-x-4 text-sm text-gray-500">
                                <span>{{ number_format($stats['total_volume'], 0) }} €</span>
                                <span>•</span>
                                <span>{{ $stats['total_positions'] }} tregtarë</span>
                            </div>
                            @if($market->resolved)
                                <div class="text-sm font-semibold text-green-600">
                                    Fitues: {{ strtoupper($market->outcome === 'yes' ? 'PO' : 'JO') }}
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('markets.show', $market) }}" 
                           class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white text-center py-3 rounded-xl font-semibold hover:opacity-90 transition-opacity">
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
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Asnjë treg i disponueshëm</h3>
                        <p class="text-gray-600 mb-6">Tregjet e parashikimeve do të jenë të disponueshme së shpejti.</p>
                        <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
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
            <div class="mt-12 bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Si Funksionojnë Tregjti të Parashikimeve</h3>
                <div class="grid md:grid-cols-3 gap-6 text-sm">
                    <div>
                        <div class="text-blue-600 dark:text-blue-400 font-semibold mb-2">📈 Bëni Parashikime</div>
                        <p class="text-gray-600 dark:text-gray-400">Blini aksione në rezultate që besoni se do të ndodhin. Çmimet reflektojnë probabilitetin e çdo rezultati.</p>
                    </div>
                    <div>
                        <div class="text-green-600 dark:text-green-400 font-semibold mb-2">🎯 Parashikimet e Sakta Fitojnë</div>
                        <p class="text-gray-600 dark:text-gray-400">Kur tregjti zgjidhen, aksionet fitüese paguajnë euro. Sa më të sakta parashikimet tuaja, aq më shumë fitoni.</p>
                    </div>
                    <div>
                        <div class="text-purple-600 dark:text-purple-400 font-semibold mb-2">🏆 Shkembeni Shpërblime</div>
                        <p class="text-gray-600 dark:text-gray-400">Përdorni eurot e fituara për të marra çmima nga dyqani ynë ose konkurroni në tregjti të reja.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>