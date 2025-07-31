<?php

use App\Http\Controllers\MarketController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\RewardController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    $featuredMarkets = \App\Models\Market::where('resolved', false)
        ->where('closes_at', '>', now())
        ->with(['positions'])
        ->orderBy('created_at', 'desc')
        ->limit(6)
        ->get();
    
    $resolvedMarkets = \App\Models\Market::where('resolved', true)
        ->with(['positions'])
        ->orderBy('updated_at', 'desc')
        ->limit(3)
        ->get();
    
    return view('welcome', compact('featuredMarkets', 'resolvedMarkets'));
})->name('home');

// Public market routes
Route::get('/markets', [MarketController::class, 'index'])->name('markets.index');
Route::get('/markets/{market}', [MarketController::class, 'show'])->name('markets.show');

// Prize store (public access)
Route::get('/prizes', function () {
    return view('prizes.index');
})->name('prizes.index');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    
    // Trading routes
    Route::post('/markets/{market}/trade', [MarketController::class, 'trade'])->name('markets.trade');
    Route::post('/positions', [PositionController::class, 'store'])->name('positions.store');
    
    // Reward routes
    Route::post('/api/reward-tokens', [RewardController::class, 'rewardTokens'])->name('api.reward-tokens');
    Route::get('/api/available-tasks', [RewardController::class, 'getAvailableTasks'])->name('api.available-tasks');
    Route::get('/api/token-history', [RewardController::class, 'getTokenHistory'])->name('api.token-history');
    
    // Settings routes
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// Admin routes
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::post('/markets/{market}/resolve', [MarketController::class, 'resolve'])->name('admin.markets.resolve');
    Route::post('/api/bonus-tokens', [RewardController::class, 'bonusTokens'])->name('admin.bonus-tokens');
});

require __DIR__.'/auth.php';

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
