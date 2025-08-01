<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 backdrop-blur-lg bg-white/95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <div class="w-9 h-9 gradient-bg rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-lg">K</span>
                        </div>
                        <span class="font-bold text-xl text-gray-900">PredictX</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('markets.index') }}" class="text-gray-700 hover:text-gray-900 font-medium text-sm">Tregjet</a>
                    <a href="{{ route('prizes.index') }}" class="text-gray-700 hover:text-gray-900 font-medium text-sm">Çmimet</a>
                    <a href="#" class="text-gray-700 hover:text-gray-900 font-medium text-sm">Rreth Nesh</a>
                </div>

                <!-- Auth Links -->
                <div class="flex items-center space-x-3">
                    @auth
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-50 border border-green-200 px-3 py-1.5 rounded-lg">
                                <span class="text-sm font-semibold text-green-700">€{{ number_format(auth()->user()->balance, 2) }}</span>
                            </div>
                            <a href="{{ route('dashboard') }}" class="gradient-bg text-white px-5 py-2 rounded-lg font-medium hover:opacity-90 transition-opacity text-sm">
                                Paneli Im
                            </a>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 font-medium text-sm px-4 py-2">Kyçuni</a>
                        <a href="{{ route('register') }}" class="gradient-bg text-white px-5 py-2 rounded-lg font-medium hover:opacity-90 transition-opacity text-sm">
                            Regjistrohuni
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative bg-white py-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="mb-8">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-purple-100 text-purple-800 mb-6">
                        🎯 Platforma e parë e parashikimeve në Kosovë
                    </span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                    Tregtoni mbi
                    <span class="text-transparent bg-clip-text gradient-bg">
                        ardhmen
                    </span>
                </h1>
                <p class="text-xl text-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed">
                    Parashikoni ngjarjet e ardhshme dhe fitoni para reale. Nga zgjedhjet dhe ekonomia deri tek sporti dhe teknologjia - mendimi juaj ka vlerë.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                    <a href="{{ route('markets.index') }}" class="gradient-bg text-white px-8 py-4 rounded-xl font-semibold hover:opacity-90 transition-opacity text-lg shadow-lg">
                        Shikoni Tregjet
                    </a>
                    <a href="#how-it-works" class="border-2 border-gray-200 text-gray-700 px-8 py-4 rounded-xl font-semibold hover:border-gray-300 hover:bg-gray-50 transition-all text-lg">
                        Si Funksionon
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900 mb-2">{{ $featuredMarkets->count() }}+</div>
                        <div class="text-gray-600 font-medium">Tregje Aktive</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900 mb-2">€10,000+</div>
                        <div class="text-gray-600 font-medium">Volum i Tregtimit</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-gray-900 mb-2">500+</div>
                        <div class="text-gray-600 font-medium">Tregtarë Aktivë</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Markets -->
    @if($featuredMarkets->count() > 0)
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Tregjet më të Popullarizuara</h2>
                <p class="text-lg text-gray-600">Tregtoni mbi ngjarjet që po diskutohen më shumë</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredMarkets->take(6) as $market)
                    @php
                        $marketMaker = app(\App\Services\MarketMaker::class);
                        $stats = $marketMaker->getMarketStats($market);
                    @endphp
                    <div class="bg-white border border-gray-200 rounded-2xl p-6 card-hover">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 line-clamp-2 text-lg mb-2">
                                    {{ $market->title }}
                                </h3>
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
                                        🔥 Aktiv
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <p class="text-gray-600 text-sm mb-6 line-clamp-2">
                            {{ $market->description }}
                        </p>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-xl border border-green-100">
                                <span class="font-medium text-green-800">PO</span>
                                <span class="font-bold text-green-700 text-lg">{{ $stats['probability_yes'] }}€</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-red-50 rounded-xl border border-red-100">
                                <span class="font-medium text-red-800">JO</span>
                                <span class="font-bold text-red-700 text-lg">{{ $stats['probability_no'] }}€</span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between text-xs text-gray-500 mb-4 bg-gray-50 p-3 rounded-lg">
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
    <section id="how-it-works" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Si Funksionon</h2>
                <p class="text-lg text-gray-600">Tre hapa të thjeshtë për të filluar tregtimin e parashikimeve</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-12">
                <div class="text-center">
                    <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <span class="text-3xl">👤</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">1. Regjistrohuni</h3>
                    <p class="text-gray-600 leading-relaxed">Krijoni llogarinë tuaj falas dhe merrni €100 virtuale për të filluar tregtimin</p>
                </div>
                
                <div class="text-center">
                    <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <span class="text-3xl">📈</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">2. Tregtoni</h3>
                    <p class="text-gray-600 leading-relaxed">Zgjidhni një ngjarje dhe blini aksione PO ose JO bazuar në mendimin tuaj</p>
                </div>
                
                <div class="text-center">
                    <div class="w-20 h-20 gradient-bg rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <span class="text-3xl">💎</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">3. Fitoni</h3>
                    <p class="text-gray-600 leading-relaxed">Nëse parashikimi juaj është i saktë, aksionet tuaja vlejnë €1 secila</p>
                </div>
            </div>
            
            <div class="text-center mt-16">
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-8 rounded-2xl">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Gati për të filluar?</h3>
                    <p class="text-gray-600 mb-6">Bashkohuni me mijëra tregtarë që po fitojnë para nga parashikimet e tyre.</p>
                    <a href="{{ route('register') }}" class="gradient-bg text-white px-8 py-4 rounded-xl font-semibold hover:opacity-90 transition-opacity text-lg inline-block">
                        Filloni Tash - Falas
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Euro Earning Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white">
                <div class="max-w-3xl">
                    <h2 class="text-3xl font-bold mb-4">Shumë Rruga për me Fitua Euro</h2>
                    <p class="text-blue-100 mb-6">Filloni me €1,000 falas dhe fitoni më shumë përmes aktiviteteve të ndryshme</p>
                    
                    <div class="grid sm:grid-cols-2 gap-4 mb-6">
                        <div class="bg-white/10 rounded-lg p-4">
                            <div class="font-semibold mb-1">Shihni Reklama</div>
                            <div class="text-sm text-blue-100">€10-25 për reklam</div>
                        </div>
                        <div class="bg-white/10 rounded-lg p-4">
                            <div class="font-semibold mb-1">Hyrje e Përditsshme</div>
                            <div class="text-sm text-blue-100">€100 çdo ditë</div>
                        </div>
                        <div class="bg-white/10 rounded-lg p-4">
                            <div class="font-semibold mb-1">Kryeni Detyra</div>
                            <div class="text-sm text-blue-100">€50-150</div>
                        </div>
                        <div class="bg-white/10 rounded-lg p-4">
                            <div class="font-semibold mb-1">Fitoni Parashikime</div>
                            <div class="text-sm text-blue-100">Potencial të pakufishem fitimi</div>
                        </div>
                    </div>
                    
                    @guest
                        <a href="{{ route('register') }}" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                            Filloni me Fitua Tash
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Results -->
    @if($resolvedMarkets->count() > 0)
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Rezultate të Fundit</h2>
                <p class="text-lg text-gray-600">Shihni si komuniteti i ka parashiku këto rezultate</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($resolvedMarkets as $market)
                    @php
                        $marketMaker = app(\App\Services\MarketMaker::class);
                        $stats = $marketMaker->getMarketStats($market);
                        $isCorrectPrediction = $market->outcome === 'yes' ? $stats['probability_yes'] > 50 : $stats['probability_no'] > 50;
                    @endphp
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="font-semibold text-gray-900 line-clamp-2 flex-1">
                                {{ $market->title }}
                            </h3>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $market->outcome === 'yes' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} ml-2">
                                {{ strtoupper($market->outcome) }}
                            </span>
                        </div>
                        
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Parashikimi i Komunitetit</span>
                                <span class="{{ $isCorrectPrediction ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $isCorrectPrediction ? 'I Saktë' : 'I Pasaktë' }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $stats['probability_yes'] }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>{{ $stats['probability_yes'] }}% PO</span>
                                <span>{{ $stats['probability_no'] }}% JO</span>
                            </div>
                        </div>
                        
                        <div class="text-xs text-gray-500">
                            <div>{{ $stats['total_positions'] }} tregtarë • Zgjidh {{ $market->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Gati me Fillu me Parashiku?</h2>
            <p class="text-lg text-gray-600 mb-8">
                Bashkohuni me mijëra përdorues që bëjnë parashikime dhe fitojnë shpërblime në PredictX
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @guest
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        Filloni - Asht Falas
                    </a>
                    <a href="{{ route('markets.index') }}" class="border border-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                        Shfletoni Tregjti
                    </a>
                @else
                    <a href="{{ route('markets.index') }}" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        Filloni me Tregtua
                    </a>
                    <a href="{{ route('prizes.index') }}" class="border border-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
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
                <p>&copy; 2024 PredictX. Krejt të drejtat të rezervuara. Ndërtuar me Laravel.</p>
            </div>
        </div>
    </footer>
</body>
</html>