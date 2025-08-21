<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Market;
use App\Models\MarketChoice;
use Illuminate\Database\Seeder;

class MarketSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure featured multi-choice market exists
        $title = 'Kush do i fitoj zgjedhjet lokale ne prishtine';
        $market = Market::firstOrCreate([
            'title' => $title,
        ], [
            'description' => 'Parashiko fituesin e zgjedhjeve lokale në Prishtinë.',
            'closes_at' => now()->addDays(30),
            'resolved' => false,
            'outcome' => 'unknown',
            'liquidity' => 2000,
            'b' => 120,
        ]);

        if ($market->choices()->count() === 0) {
            $choices = [
                ['name' => 'Përparim Rama', 'party' => 'LDK (incumbent)', 'notes' => 'Running for a second term'],
                ['name' => 'Hajrulla Çeku', 'party' => 'LVV', 'notes' => 'Minister of Culture'],
                ['name' => 'Uran Ismaili', 'party' => 'PDK', 'notes' => 'Ex‑Minister of Health'],
                ['name' => 'Bekë Berisha', 'party' => 'AAK', 'notes' => 'Known from parliamentary campaign'],
                ['name' => 'Besa Shahini', 'party' => 'PSD', 'notes' => 'Former Albanian Education Minister, female'],
                ['name' => 'Fatmir Selimi', 'party' => 'Independent', 'notes' => 'Recently declared candidacy'],
            ];
            foreach ($choices as $c) {
                MarketChoice::firstOrCreate([
                    'market_id' => $market->id,
                    'slug' => str($c['name'])->slug('-'),
                ], [
                    'name' => $c['name'],
                    'party' => $c['party'],
                    'notes' => $c['notes'],
                ]);
            }
        }
    }
}
