<?php

declare(strict_types=1);

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\ProjectStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            /** Match the public site's ordering so the admin reflects reality. */
            ->defaultSort('sort_order')
            /** The generated table listed every column; most are noise in a list view. */
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('')
                    ->disk('public')
                    ->square()
                    ->size(44),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record): string => $record->tagline)
                    ->wrap(),

                /** Label and colour both come from the enum's Filament contracts. */
                TextColumn::make('status')
                    ->badge(),

                /** Derived, not stored — draft vs published is published_at's job. */
                TextColumn::make('published_at')
                    ->label('Visibility')
                    ->badge()
                    ->state(fn ($record): string => $record->isPublished() ? 'Published' : 'Draft')
                    ->color(fn ($record): string => $record->isPublished() ? 'success' : 'gray'),

                TextColumn::make('technologies.name')
                    ->badge()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->label('Stack'),

                TextColumn::make('client')
                    ->placeholder('Personal')
                    ->toggleable(),

                TextColumn::make('year')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->dateTime('j M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ProjectStatus::class),

                TernaryFilter::make('is_featured')
                    ->label('Featured'),

                Filter::make('drafts')
                    ->label('Drafts only')
                    ->query(fn (Builder $query): Builder => $query->whereNull('published_at')),

                Filter::make('needs_case_study')
                    ->label('Missing case study')
                    ->query(fn (Builder $query): Builder => $query->whereNull('body')),
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
