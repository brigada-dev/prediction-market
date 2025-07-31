<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Market;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MarketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $markets = [
            [
                'title' => 'A do ta baj Albin Kurti denoncim tu rrit miza n\'biçiklet?',
                'description' => 'Ky market përfundon me PO nëse Albin Kurti do të shijojë denoncim publik për tu angazhuar në aktivitete ekstreme të rritjes së mizave në biçikletë para 31 Dhjetor 2025.',
                'closes_at' => Carbon::parse('2025-12-31 23:59:59'),
                'resolved' => false,
                'outcome' => 'unknown',
                'liquidity' => 5000,
                'b' => 150,
            ],
            [
                'title' => 'A don me dal Dua Lipa në Big Brother VIP Kosova?',
                'description' => 'Ky market përfundon me PO nëse Dua Lipa do të shfaqet si mysafir special apo konkurent në Big Brother VIP Kosova brenda vitit 2025.',
                'closes_at' => Carbon::parse('2025-12-31 23:59:59'),
                'resolved' => false,
                'outcome' => 'unknown',
                'liquidity' => 3000,
                'b' => 120,
            ],
            [
                'title' => 'A don me u martu Edi Rama me Vjosa Osmanin që me zgjatu mandatin?',
                'description' => 'Ky market përfundon me PO nëse Edi Rama do të propozojë martesë politike me Vjosa Osmanin si strategi për të zgjarë mandatin e tij para vitit 2026.',
                'closes_at' => Carbon::parse('2026-06-30 23:59:59'),
                'resolved' => false,
                'outcome' => 'unknown',
                'liquidity' => 2000,
                'b' => 100,
            ],
            [
                'title' => 'A ka me fitua Kosova Kupën e Botës në futboll elektronik?',
                'description' => 'Ky market përfundon me PO nëse Kosova do të fitojë Kupën e Botës në FIFA eFootball brenda vitit 2025.',
                'closes_at' => Carbon::parse('2025-12-31 23:59:59'),
                'resolved' => true,
                'outcome' => 'yes',
                'liquidity' => 4000,
                'b' => 130,
            ],
            [
                'title' => 'A don me hec Rita Ora zbathun n\'rrugat e Prishtinës?',
                'description' => 'Ky market përfundon me PO nëse Rita Ora do të organizojë një shëtitje të veçantë zbathun nëpër qendër të Prishtinës si protestë artistike brenda 2025.',
                'closes_at' => Carbon::parse('2025-08-15 23:59:59'),
                'resolved' => false,
                'outcome' => 'unknown',
                'liquidity' => 1500,
                'b' => 80,
            ],
            [
                'title' => 'A ka me u kthy Hashim Thaçi në politikë si influencer TikTok?',
                'description' => 'Ky market përfundon me PO nëse Hashim Thaçi do të lansojë karrierë si influencer TikTok dhe do të marrë mbi 1 milion followers brenda vitit 2026.',
                'closes_at' => Carbon::parse('2026-12-31 23:59:59'),
                'resolved' => false,
                'outcome' => 'unknown',
                'liquidity' => 8000,
                'b' => 200,
            ],
            [
                'title' => 'A don me hapë Besa Luci kanal YouTube "Kuzhinat e Sllatinës"?',
                'description' => 'Ky market përfundon me PO nëse Besa Luci do të krijojë kanal YouTube të dedikuar recetave tradicionale të Sllatinës dhe do të marrë mbi 100k abonentë brenda 2025.',
                'closes_at' => Carbon::parse('2025-10-31 23:59:59'),
                'resolved' => false,
                'outcome' => 'unknown',
                'liquidity' => 2500,
                'b' => 110,
            ],
            [
                'title' => 'A ka me u ba Ramush Haradinaj chef kuzhinier në MasterChef Albania?',
                'description' => 'Ky market përfundon me PO nëse Ramush Haradinaj do të shfaqet si gjyqtar special apo chef në MasterChef Albania brenda sezonit të ardhshëm.',
                'closes_at' => Carbon::parse('2025-09-30 23:59:59'),
                'resolved' => false,
                'outcome' => 'unknown',
                'liquidity' => 3500,
                'b' => 125,
            ],
        ];

        foreach ($markets as $marketData) {
            Market::create($marketData);
        }
    }
}
