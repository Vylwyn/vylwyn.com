<?php

declare(strict_types=1);

namespace App\Filament\Resources\Technologies\Tables;

use App\Enums\TechnologyCategory;
use App\Models\Technology;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class TechnologiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            /**
             * Grouping by a column cast to an enum can't use the shorthand
             * ->defaultGroup('category') — Filament expects a string for the
             * group heading and receives a TechnologyCategory instance instead.
             * Resolving the title explicitly also lets us use the enum's label().
             */
            ->defaultGroup(
                Group::make('category')
                    ->label('Category')
                    ->getTitleFromRecordUsing(
                        fn (Technology $record): string => $record->category->getLabel()
                    )
            )
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category')
                    ->badge()
                    ->sortable(),

                IconColumn::make('show_in_skills')
                    ->label('In skills grid')
                    ->boolean()
                    ->sortable(),

                /**
                 * withCount on the relationship — one aggregate query rather than
                 * one query per row. This is the N+1 problem, avoided.
                 */
                TextColumn::make('projects_count')
                    ->counts('projects')
                    ->label('Projects')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(TechnologyCategory::class),

                TernaryFilter::make('show_in_skills')
                    ->label('Shown in skills'),
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
