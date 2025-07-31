<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrizeClaimResource\Pages;
use App\Filament\Resources\PrizeClaimResource\RelationManagers;
use App\Models\PrizeClaim;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrizeClaimResource extends Resource
{
    protected static ?string $model = PrizeClaim::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Prize Management';

    protected static ?string $label = 'Prize Claims';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('prize_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('prize_description')
                    ->maxLength(255),
                Forms\Components\TextInput::make('token_cost')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'shipped' => 'Shipped',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('admin_notes')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('claimed_at'),
                Forms\Components\DateTimePicker::make('fulfilled_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prize_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('token_cost')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'primary' => 'shipped',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('claimed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fulfilled_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'shipped' => 'Shipped',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (PrizeClaim $record) => $record->status === 'pending')
                    ->action(fn (PrizeClaim $record) => $record->update(['status' => 'approved'])),
                Tables\Actions\Action::make('ship')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->visible(fn (PrizeClaim $record) => $record->status === 'approved')
                    ->action(fn (PrizeClaim $record) => $record->update([
                        'status' => 'shipped',
                        'fulfilled_at' => now(),
                    ])),
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
            'index' => Pages\ListPrizeClaims::route('/'),
            'create' => Pages\CreatePrizeClaim::route('/create'),
            'edit' => Pages\EditPrizeClaim::route('/{record}/edit'),
        ];
    }
}
