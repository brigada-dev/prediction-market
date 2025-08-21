<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\RewardController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', HomeController::class)->name('home');

// Debug route for theme testing
Route::get('/debug-theme', function () {
    return view('debug-theme');
})->name('debug-theme');

// Alpine.js test route
Route::get('/test-alpine', function () {
    return view('test-alpine');
})->name('test-alpine');

// Public market routes
Route::get('/markets', [MarketController::class, 'index'])->name('markets.index');
Route::get('/markets/{market}', [MarketController::class, 'show'])->name('markets.show');

// Prize store (public access)
Route::get('/prizes', function () {
    return view('prizes.index');
})->name('prizes.index');

// API routes (no auth required for market data)
Route::prefix('api')->group(function () {
    Route::get('/markets/{market}/historical-prices', [MarketController::class, 'getHistoricalPrices'])->name('api.markets.historical-prices');
});

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
