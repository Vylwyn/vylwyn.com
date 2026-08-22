<?php

declare(strict_types=1);

namespace App\Filament\Resources\Experiences\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExperiencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('role')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record): string => $record->organisation)
                    ->wrap(),

                TextColumn::make('location')
                    ->placeholder('—')
                    ->toggleable(),

                /** Formatted by the model, so admin and public site can never disagree. */
                TextColumn::make('period')
                    ->state(fn ($record): string => $record->period())
                    ->label('Period'),

                TextColumn::make('duration')
                    ->state(fn ($record): string => $record->duration())
                    ->label('Duration'),

                TextColumn::make('current')
                    ->label('')
                    ->badge()
                    ->state(fn ($record): ?string => $record->isCurrent() ? 'Current' : null)
                    ->color('success')
                    ->placeholder(''),

                TextColumn::make('summary')
                    ->limit(50)
                    ->placeholder('Not written yet')
                    ->toggleable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('current')
                    ->label('Current role only')
                    ->query(fn (Builder $query): Builder => $query->whereNull('ended_on')),

                Filter::make('missing_summary')
                    ->label('Missing summary')
                    ->query(fn (Builder $query): Builder => $query->whereNull('summary')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
