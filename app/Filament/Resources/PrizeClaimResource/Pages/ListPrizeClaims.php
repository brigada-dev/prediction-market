<?php

namespace App\Filament\Resources\PrizeClaimResource\Pages;

use App\Filament\Resources\PrizeClaimResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrizeClaims extends ListRecords
{
    protected static string $resource = PrizeClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
