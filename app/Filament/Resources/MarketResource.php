<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarketResource\Pages;
use App\Filament\Resources\MarketResource\RelationManagers;
use App\Models\Market;
use App\Services\MarketMaker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MarketResource extends Resource
{
    protected static ?string $model = Market::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Market Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('closes_at')
                    ->required(),
                Forms\Components\Toggle::make('resolved')
                    ->required(),
                Forms\Components\Select::make('outcome')
                    ->options([
                        'yes' => 'Po',
                        'no' => 'Jo',
                        'unknown' => 'I Panjohur',
                    ])
                    ->default('unknown')
                    ->required(),
                Forms\Components\TextInput::make('liquidity')
                    ->required()
                    ->numeric()
                    ->default(1000),
                Forms\Components\TextInput::make('b')
                    ->required()
                    ->numeric()
                    ->default(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('closes_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('resolved')
                    ->boolean(),
                Tables\Columns\TextColumn::make('outcome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('liquidity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('b')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('resolve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Market $record) => !$record->resolved)
                    ->form([
                        Forms\Components\Select::make('outcome')
                            ->options([
                                'yes' => 'Yes',
                                'no' => 'No',
                            ])
                            ->required(),
                    ])
                    ->action(function (Market $record, array $data): void {
                        $record->update([
                            'resolved' => true,
                            'outcome' => $data['outcome'],
                        ]);

                        // Settle the market
                        app(MarketMaker::class)->settleMarket($record);

                        Notification::make()
                            ->success()
                            ->title('Market resolved and settled successfully')
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMarkets::route('/'),
            'create' => Pages\CreateMarket::route('/create'),
            'edit' => Pages\EditMarket::route('/{record}/edit'),
        ];
    }
}
