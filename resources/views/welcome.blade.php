<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="transition-theme">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PredictX - Platforma për Parashikime në Kosovë</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .dark .gradient-bg {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        .dark .card-hover:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
    </style>
    <!-- Theme initialization script -->
    <script>
        // Initialize theme on page load
        (function() {
            try {
                const theme = localStorage.getItem('theme') || 'light';
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (error) {
                console.warn('Theme initialization failed:', error);
                // Fallback to light mode
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-theme">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50 backdrop-blur-lg bg-white/95 dark:bg-gray-800/95 transition-theme">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <div class="w-9 h-9 gradient-bg rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-lg">K</span>
                        </div>
                        <span class="font-bold text-xl text-gray-900 dark:text-white transition-theme">PredictX</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('markets.index') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium text-sm transition-theme">Tregjet</a>
                    <a href="{{ route('prizes.index') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium text-sm transition-theme">Çmimet</a>
                    <a href="#" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium text-sm transition-theme">Rreth Nesh</a>
                </div>

                <!-- Auth Links -->
                <div class="flex items-center space-x-3">
                    <!-- Theme Toggle -->
                    <x-simple-theme-toggle />
                    
                    @auth
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 px-3 py-1.5 rounded-lg transition-theme">
                                <span class="text-sm font-semibold text-green-700 dark:text-green-300">€{{ number_format(auth()->user()->balance, 2) }}</span>
                            </div>
                            <a href="{{ route('dashboard') }}" class="gradient-bg text-white px-5 py-2 rounded-lg font-medium hover:opacity-90 transition-opacity text-sm">
                                Paneli Im
                            </a>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium text-sm px-4 py-2 transition-theme">Kyçuni</a>
                        <a href="{{ route('register') }}" class="gradient-bg text-white px-5 py-2 rounded-lg font-medium hover:opacity-90 transition-opacity text-sm">
                            Regjistrohuni
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Focused Hero: Featured Market -->
    <section class="relative overflow-hidden bg-gray-900 py-24 transition-theme">
        <!-- Background candidate silhouettes (replace with actual images) -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute -left-10 top-10 w-60 h-60 rounded-full brand-gradient blur-3xl"></div>
            <div class="absolute right-0 bottom-10 w-72 h-72 rounded-full brand-gradient blur-3xl"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="grid lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-white/10 text-white mb-6">
                        🎯 Treg i Veçuar
                    </span>
                    <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight mb-4">
                        {{ $featuredMarket?->title ?? 'Treg i Veçuar' }}
                    </h1>
                    <p class="text-lg text-gray-200 max-w-2xl mb-8">
                        {{ $featuredMarket?->description ?? 'Treg i hapur aktualisht.' }}
                    </p>
                    @if($featuredMarket)
                        @php
                            $mm = app(\App\Services\MarketMaker::class);
                            $stats = $mm->getMarketStats($featuredMarket);
                        @endphp
                        @if($featuredMarket->choices()->exists())
                            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 max-w-3xl mb-8">
                                @foreach($featuredMarket->choices as $choice)
                                    @php 
                                        $slug = $choice->slug; 
                                        $prob = $stats['choice_probabilities'][$slug] ?? 0; 
                                        $price = $mm->costToBuy($featuredMarket, $slug, 1);
                                    @endphp
                                    <div class="rounded-xl border border-white/20 bg-white/5 p-4 text-white">
                                        <div class="font-semibold">{{ $choice->name }}</div>
                                        <div class="text-xs text-white/70">{{ $choice->party }}</div>
                                        <div class="mt-2 flex justify-between text-sm"><span>Prob.</span><span class="font-bold">{{ $prob }}%</span></div>
                                        <div class="flex justify-between text-sm"><span>Çmimi</span><span class="font-semibold">€{{ number_format($price,2) }}</span></div>
                                        <div class="flex justify-between text-sm"><span>Fito</span><span class="font-semibold">€{{ number_format(1 - $price,2) }}</span></div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            @php $priceYes = $mm->costToBuy($featuredMarket, 'yes', 1); $priceNo = $mm->costToBuy($featuredMarket, 'no', 1); @endphp
                            <div class="grid grid-cols-2 gap-4 max-w-xl mb-8">
                                <div class="rounded-xl border border-green-500/40 bg-green-500/10 p-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-green-300 font-semibold">PO</span>
                                        <span class="text-green-200 text-sm">{{ $stats['probability_yes'] }}%</span>
                                    </div>
                                    <div class="mt-2 text-sm text-green-100">Çmimi/aksion: <span class="font-semibold">€{{ number_format($priceYes, 2) }}</span></div>
                                    <div class="text-sm text-green-100">Pagesa nëse fiton: <span class="font-semibold">€1.00</span></div>
                                </div>
                                <div class="rounded-xl border border-red-500/40 bg-red-500/10 p-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-red-300 font-semibold">JO</span>
                                        <span class="text-red-200 text-sm">{{ $stats['probability_no'] }}%</span>
                                    </div>
                                    <div class="mt-2 text-sm text-red-100">Çmimi/aksion: <span class="font-semibold">€{{ number_format($priceNo, 2) }}</span></div>
                                    <div class="text-sm text-red-100">Pagesa nëse fiton: <span class="font-semibold">€1.00</span></div>
                                </div>
                            </div>
                        @endif
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('markets.show', $featuredMarket) }}" class="btn-brand neon-glow inline-flex items-center justify-center">
                                Parashiko Tash
                            </a>
                            <a href="#how-it-works" class="border border-white/20 text-white px-6 py-3 rounded-xl font-semibold hover:bg-white/10">Si Funksionon</a>
                        </div>
                    @endif
                </div>
                <div class="lg:col-span-5">
                    <!-- Placeholder collage: replace with candidate images -->
                    <div class="relative aspect-[4/5] rounded-3xl overflow-hidden border border-white/10 shadow-2xl">
                        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/20 to-black/40"></div>
                        <div class="absolute inset-0 grid grid-cols-3 gap-1 opacity-60">
                            <div class="bg-white/10">
                                <img src="{{ asset('images/perparim-rama.png') }}" alt="Përparim Rama" class="w-full h-full object-cover grayscale">
                            </div>
                            <div class="bg-white/10">
                                <img src="{{ asset('images/hajrulla-ceku.png') }}" alt="Hajrulla Cekaj" class="w-full h-full object-cover grayscale">
                            </div>
                            <div class="bg-white/10">
                                <img src="{{ asset('images/uran-ismaili.png') }}" alt="Uran Ismaili" class="w-full h-full object-cover grayscale">
                            </div>
                            <div class="bg-white/10">
                                <img src="{{ asset('images/besa-shahini.png') }}" alt="Besa Shahini" class="w-full h-full object-cover grayscale">
                            </div>
                            <div class="bg-white/10">
                                <img src="{{ asset('images/beke-berisha.png') }}" alt="Beke Berisha" class="w-full h-full object-cover grayscale">
                            </div>
                            <div class="bg-white/10">
                                <img src="{{ asset('images/fatmir-selimi.png') }}" alt="Fatmir Selimi" class="w-full h-full object-cover grayscale">
                            </div>
                        </div>
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <div class="text-sm text-white/80">Fushata në Prishtinë</div>
                            <div class="text-xl font-semibold">Kandidatët në garë</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Markets -->
    @if($featuredMarkets->count() > 0)
    <section class="py-20 bg-gray-50 dark:bg-gray-900 transition-theme">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4 transition-theme">Tregjet më të Popullarizuara</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 transition-theme">Tregtoni mbi ngjarjet që po diskutohen më shumë</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredMarkets->take(6) as $market)
                    @php
                        $marketMaker = app(\App\Services\MarketMaker::class);
                        $stats = $marketMaker->getMarketStats($market);
                    @endphp
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 card-hover transition-theme">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 dark:text-white line-clamp-2 text-lg mb-2 transition-theme">
                                    {{ $market->title }}
                                </h3>
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
                                        🔥 Aktiv
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-6 line-clamp-2 transition-theme">
                            {{ $market->description }}
                        </p>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between items-center p-3 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-700 transition-theme">
                                <span class="font-medium text-green-800 dark:text-green-300">PO</span>
                                <span class="font-bold text-green-700 dark:text-green-300 text-lg">{{ $stats['probability_yes'] }}€</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-100 dark:border-red-700 transition-theme">
                                <span class="font-medium text-red-800 dark:text-red-300">JO</span>
                                <span class="font-bold text-red-700 dark:text-red-300 text-lg">{{ $stats['probability_no'] }}€</span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-4 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg transition-theme">
                            <span>{{ number_format($stats['total_volume'], 0) }} €</span>
                            <span>{{ $market->closes_at->format('M j') }}</span>
                        </div>
                        
                        <a href="{{ route('markets.show', $market) }}" 
                           class="block w-full text-center gradient-bg text-white py-3 rounded-xl font-semibold hover:opacity-90 transition-opacity">
                            Tregtoni
                        </a>
                    </div>
                @endforeach
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('markets.index') }}" 
                   class="inline-flex items-center gradient-bg text-white px-8 py-3 rounded-xl font-semibold hover:opacity-90 transition-opacity">
                    Shikoni të gjitha tregjet
                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-20 bg-white dark:bg-gray-800 transition-theme">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4 transition-theme">Si Funksionon</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 transition-theme">Tre hapa të thjeshtë për të filluar tregtimin e parashikimeve</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-12">
                <div class="text-center">
                    <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <span class="text-3xl">👤</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 transition-theme">1. Regjistrohuni</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed transition-theme">Krijoni llogarinë tuaj falas dhe merrni €100 virtuale për të filluar tregtimin</p>
                </div>
                
                <div class="text-center">
                    <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <span class="text-3xl">📈</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 transition-theme">2. Tregtoni</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed transition-theme">Zgjidhni një ngjarje dhe blini aksione PO ose JO bazuar në mendimin tuaj</p>
                </div>
                
                <div class="text-center">
                    <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <span class="text-3xl">💎</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 transition-theme">3. Fitoni</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed transition-theme">Nëse parashikimi juaj është i saktë, aksionet tuaja vlejnë €1 secila</p>
                </div>
            </div>
            
            <div class="text-center mt-16">
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 p-8 rounded-2xl transition-theme">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 transition-theme">Gati për të filluar?</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 transition-theme">Bashkohuni me mijëra tregtarë që po fitojnë para nga parashikimet e tyre.</p>
                    <a href="{{ route('register') }}" class="gradient-bg text-white px-8 py-4 rounded-xl font-semibold hover:opacity-90 transition-opacity text-lg inline-block">
                        Filloni Tash - Falas
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Euro Earning Section -->
    <section class="py-16 bg-white dark:bg-gray-800 transition-theme">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-500 dark:to-purple-500 rounded-2xl p-8 text-white">
                <div class="max-w-3xl">
                    <h2 class="text-3xl font-bold mb-4">Shumë Rruga për me Fitua Euro</h2>
                    <p class="text-blue-100 dark:text-blue-200 mb-6 transition-theme">Filloni me €1,000 falas dhe fitoni më shumë përmes aktiviteteve të ndryshme</p>
                    
                    <div class="grid sm:grid-cols-2 gap-4 mb-6">
                        <div class="bg-white/10 rounded-lg p-4">
                            <div class="font-semibold mb-1">Shihni Reklama</div>
                            <div class="text-sm text-blue-100 dark:text-blue-200">€10-25 për reklam</div>
                        </div>
                        <div class="bg-white/10 rounded-lg p-4">
                            <div class="font-semibold mb-1">Hyrje e Përditsshme</div>
                            <div class="text-sm text-blue-100 dark:text-blue-200">€100 çdo ditë</div>
                        </div>
                        <div class="bg-white/10 rounded-lg p-4">
                            <div class="font-semibold mb-1">Kryeni Detyra</div>
                            <div class="text-sm text-blue-100 dark:text-blue-200">€50-150</div>
                        </div>
                        <div class="bg-white/10 rounded-lg p-4">
                            <div class="font-semibold mb-1">Fitoni Parashikime</div>
                            <div class="text-sm text-blue-100 dark:text-blue-200">Potencial të pakufishem fitimi</div>
                        </div>
                    </div>
                    
                    @guest
                        <a href="{{ route('register') }}" class="bg-white dark:bg-gray-100 text-blue-600 dark:text-blue-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-200 transition-colors">
                            Filloni me Fitua Tash
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Results -->
    @if($resolvedMarkets->count() > 0)
    <section class="py-16 bg-gray-50 dark:bg-gray-900 transition-theme">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4 transition-theme">Rezultate të Fundit</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 transition-theme">Shihni si komuniteti i ka parashiku këto rezultate</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($resolvedMarkets as $market)
                    @php
                        $marketMaker = app(\App\Services\MarketMaker::class);
                        $stats = $marketMaker->getMarketStats($market);
                        $isCorrectPrediction = $market->outcome === 'yes' ? $stats['probability_yes'] > 50 : $stats['probability_no'] > 50;
                    @endphp
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 transition-theme">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="font-semibold text-gray-900 dark:text-white line-clamp-2 flex-1 transition-theme">
                                {{ $market->title }}
                            </h3>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $market->outcome === 'yes' ? 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300' }} ml-2 transition-theme">
                                {{ strtoupper($market->outcome) }}
                            </span>
                        </div>
                        
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1 transition-theme">
                                <span>Parashikimi i Komunitetit</span>
                                <span class="{{ $isCorrectPrediction ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} transition-theme">
                                    {{ $isCorrectPrediction ? 'I Saktë' : 'I Pasaktë' }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 transition-theme">
                                <div class="bg-green-600 dark:bg-green-500 h-2 rounded-full" style="width: {{ $stats['probability_yes'] }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1 transition-theme">
                                <span>{{ $stats['probability_yes'] }}% PO</span>
                                <span>{{ $stats['probability_no'] }}% JO</span>
                            </div>
                        </div>
                        
                        <div class="text-xs text-gray-500 dark:text-gray-400 transition-theme">
                            <div>{{ $stats['total_positions'] }} tregtarë • Zgjidh {{ $market->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <section class="py-16 bg-white dark:bg-gray-800 transition-theme">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4 transition-theme">Gati me Fillu me Parashiku?</h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 transition-theme">
                Bashkohuni me mijëra përdorues që bëjnë parashikime dhe fitojnë shpërblime në PredictX
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @guest
                    <a href="{{ route('register') }}" class="bg-blue-600 dark:bg-blue-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                        Filloni - Asht Falas
                    </a>
                    <a href="{{ route('markets.index') }}" class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Shfletoni Tregjti
                    </a>
                @else
                    <a href="{{ route('markets.index') }}" class="bg-blue-600 dark:bg-blue-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                        Filloni me Tregtua
                    </a>
                    <a href="{{ route('prizes.index') }}" class="border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Shihni Çmimet
                    </a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">P</span>
                        </div>
                        <span class="font-bold text-xl">PredictX</span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        Ku tregjti takojnë parashikimet. Tregtoni mbi të ardhmen me besim.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Tregjti</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('markets.index') }}" class="hover:text-white">Krejt Tregjti</a></li>
                        <li><a href="#" class="hover:text-white">Teknologji</a></li>
                        <li><a href="#" class="hover:text-white">Financa</a></li>
                        <li><a href="#" class="hover:text-white">Sport</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Platforma</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('prizes.index') }}" class="hover:text-white">Dyqani i Çmimeve</a></li>
                        <li><a href="#how-it-works" class="hover:text-white">Si Funksionon</a></li>
                        <li><a href="#" class="hover:text-white">API</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4">Përkrahja</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white">Qendra e Ndihmës</a></li>
                        <li><a href="#" class="hover:text-white">Kushtet e Shërbimit</a></li>
                        <li><a href="#" class="hover:text-white">Politika e Privatësisë</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; 2024 parashiko.com. Krejt të drejtat të rezervuara. Ndërtuar me Laravel.</p>
            </div>
        </div>
    </footer>
</body>
</html>