<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Market;
use Illuminate\Contracts\View\View;

class HomeController
{
    public function __invoke(): View
    {
        $featuredMarkets = Market::query()
            ->where('resolved', false)
            ->where('closes_at', '>', now())
            ->with('positions')
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        $featuredMarket = Market::query()
            ->where('title', 'Kush do i fitoj zgjedhjet lokale ne prishtine')
            ->first();

        if ($featuredMarket === null) {
            $featuredMarket = Market::query()
                ->where('resolved', false)
                ->where('closes_at', '>', now())
                ->orderByDesc('created_at')
                ->first();
        }

        $resolvedMarkets = Market::query()
            ->where('resolved', true)
            ->with('positions')
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get();

        return view('welcome', compact('featuredMarket', 'featuredMarkets', 'resolvedMarkets'));
    }
}


