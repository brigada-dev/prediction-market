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
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MarketResource extends Resource
{
    protected static ?string $model = Market::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Trading';
    
    protected static ?int $navigationSort = 2;

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
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->limit(50),
                Tables\Columns\TextColumn::make('total_volume')
                    ->label('Volume')
                    ->getStateUsing(fn (Market $record): string => '€' . number_format($record->positions->sum('cost'), 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_positions')
                    ->label('Trades')
                    ->getStateUsing(fn (Market $record): int => $record->positions->count())
                    ->sortable(),
                Tables\Columns\TextColumn::make('unique_traders')
                    ->label('Traders')
                    ->getStateUsing(fn (Market $record): int => $record->positions->unique('user_id')->count())
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('market_type')
                    ->label('Type')
                    ->getStateUsing(fn (Market $record): string => $record->choices()->exists() ? 'Multi-Choice' : 'Binary')
                    ->colors([
                        'primary' => 'Binary',
                        'warning' => 'Multi-Choice',
                    ]),
                Tables\Columns\BadgeColumn::make('resolved')
                    ->label('Status')
                    ->getStateUsing(fn (Market $record): string => $record->resolved ? 'Resolved' : 'Active')
                    ->colors([
                        'success' => 'Resolved',
                        'primary' => 'Active',
                    ]),
                Tables\Columns\TextColumn::make('outcome')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'yes' => 'success',
                        'no' => 'danger',
                        default => 'gray'
                    }),
                Tables\Columns\TextColumn::make('closes_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('resolved')
                    ->options([
                        true => 'Resolved',
                        false => 'Active',
                    ])
                    ->label('Status'),
                SelectFilter::make('market_type')
                    ->label('Market Type')
                    ->options([
                        'binary' => 'Binary',
                        'multi_choice' => 'Multi-Choice',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'] === 'binary',
                                fn (Builder $query): Builder => $query->whereDoesntHave('choices'),
                            )
                            ->when(
                                $data['value'] === 'multi_choice',
                                fn (Builder $query): Builder => $query->whereHas('choices'),
                            );
                    }),
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
