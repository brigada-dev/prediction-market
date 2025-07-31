<?php

namespace App\Filament\Resources\PrizeClaimResource\Pages;

use App\Filament\Resources\PrizeClaimResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrizeClaim extends EditRecord
{
    protected static string $resource = PrizeClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
