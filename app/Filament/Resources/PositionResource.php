<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PositionResource\Pages;
use App\Filament\Resources\PositionResource\RelationManagers;
use App\Models\Position;
use App\Models\Market;
use App\Services\MarketMaker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    
    protected static ?string $navigationLabel = 'Trades';
    
    protected static ?string $navigationGroup = 'Trading';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('market_id')
                    ->relationship('market', 'title')
                    ->required(),
                Forms\Components\TextInput::make('choice'),
                Forms\Components\TextInput::make('shares')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('cost')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('choice_id')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('market.title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('choice_display')
                    ->label('Choice')
                    ->getStateUsing(function (Position $record): string {
                        if ($record->marketChoice) {
                            return $record->marketChoice->name;
                        }
                        return strtoupper($record->choice);
                    })
                    ->badge()
                    ->color(fn (Position $record): string => match($record->choice) {
                        'yes' => 'success',
                        'no' => 'danger',
                        default => 'primary'
                    }),
                Tables\Columns\TextColumn::make('shares')
                    ->numeric(2)
                    ->sortable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('cost')
                    ->money('EUR')
                    ->sortable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('avg_price')
                    ->label('Avg Price')
                    ->getStateUsing(fn (Position $record): string => '€' . number_format($record->cost / $record->shares, 4))
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_value')
                    ->label('Current Value')
                    ->getStateUsing(function (Position $record): string {
                        $marketMaker = app(MarketMaker::class);
                        $market = $record->market;
                        
                        if ($market->resolved) {
                            if ($market->outcome === $record->choice || 
                                ($record->marketChoice && $market->outcome === $record->marketChoice->slug)) {
                                return '€' . number_format((float) $record->shares, 2);
                            }
                            return '€0.00';
                        }
                        
                        $prices = $marketMaker->price($market);
                        if ($record->marketChoice) {
                            $currentPrice = $prices[$record->marketChoice->slug] ?? 0;
                        } else {
                            $currentPrice = $prices[$record->choice] ?? 0;
                        }
                        
                        return '€' . number_format((float) $record->shares * $currentPrice, 2);
                    }),
                Tables\Columns\TextColumn::make('profit_loss')
                    ->label('P&L')
                    ->getStateUsing(function (Position $record): string {
                        $marketMaker = app(MarketMaker::class);
                        $market = $record->market;
                        $currentValue = 0;
                        
                        if ($market->resolved) {
                            if ($market->outcome === $record->choice || 
                                ($record->marketChoice && $market->outcome === $record->marketChoice->slug)) {
                                $currentValue = (float) $record->shares;
                            }
                        } else {
                            $prices = $marketMaker->price($market);
                            if ($record->marketChoice) {
                                $currentPrice = $prices[$record->marketChoice->slug] ?? 0;
                            } else {
                                $currentPrice = $prices[$record->choice] ?? 0;
                            }
                            $currentValue = (float) $record->shares * $currentPrice;
                        }
                        
                        $pnl = $currentValue - $record->cost;
                        return ($pnl >= 0 ? '+' : '') . '€' . number_format($pnl, 2);
                    })
                    ->color(function (Position $record): string {
                        $marketMaker = app(MarketMaker::class);
                        $market = $record->market;
                        $currentValue = 0;
                        
                        if ($market->resolved) {
                            if ($market->outcome === $record->choice || 
                                ($record->marketChoice && $market->outcome === $record->marketChoice->slug)) {
                                $currentValue = (float) $record->shares;
                            }
                        } else {
                            $prices = $marketMaker->price($market);
                            if ($record->marketChoice) {
                                $currentPrice = $prices[$record->marketChoice->slug] ?? 0;
                            } else {
                                $currentPrice = $prices[$record->choice] ?? 0;
                            }
                            $currentValue = (float) $record->shares * $currentPrice;
                        }
                        
                        return ($currentValue - $record->cost) >= 0 ? 'success' : 'danger';
                    }),
                Tables\Columns\BadgeColumn::make('market.resolved')
                    ->label('Status')
                    ->getStateUsing(fn (Position $record): string => $record->market->resolved ? 'Resolved' : 'Active')
                    ->colors([
                        'success' => 'Resolved',
                        'primary' => 'Active',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Trade Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('market')
                    ->relationship('market', 'title')
                    ->searchable(),
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable(),
                SelectFilter::make('market_status')
                    ->label('Market Status')
                    ->options([
                        'active' => 'Active',
                        'resolved' => 'Resolved',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'] === 'active',
                                fn (Builder $query): Builder => $query->whereHas('market', fn($q) => $q->where('resolved', false)),
                            )
                            ->when(
                                $data['value'] === 'resolved',
                                fn (Builder $query): Builder => $query->whereHas('market', fn($q) => $q->where('resolved', true)),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPositions::route('/'),
            'create' => Pages\CreatePosition::route('/create'),
            'edit' => Pages\EditPosition::route('/{record}/edit'),
        ];
    }
}
