<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->poll('60s')
            /** Unread rows stand out without needing a separate column. */
            ->recordClasses(fn (ContactMessage $record): ?string => $record->isRead() ? null : 'font-semibold')
            ->columns([
                IconColumn::make('read_at')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-s-envelope')
                    ->trueColor('gray')
                    ->falseColor('warning')
                    ->state(fn (ContactMessage $record): bool => $record->isRead()),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ContactMessage $record): string => $record->email),

                TextColumn::make('subject')
                    ->searchable()
                    ->placeholder('No subject')
                    ->limit(40),

                TextColumn::make('message')
                    ->limit(60)
                    ->wrap()
                    ->searchable()
                    ->toggleable(),

                /**
                 * Surfaces the "saved but not emailed" state. Without this
                 * column a silent SMTP failure would be invisible.
                 */
                IconColumn::make('notified')
                    ->label('Emailed')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (ContactMessage $record): string => $record->notified
                        ? 'Notification email sent'
                        : 'Notification email FAILED — check mail config'),

                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('j M Y, H:i')
                    ->description(fn (ContactMessage $record): string => $record->created_at->diffForHumans())
                    ->sortable(),

                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('unread')
                    ->label('Unread only')
                    ->query(fn (Builder $query): Builder => $query->whereNull('read_at')),

                Filter::make('failed_notification')
                    ->label('Email failed')
                    ->query(fn (Builder $query): Builder => $query->where('notified', false)),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('markAsRead')
                    ->label('Mark read')
                    ->icon('heroicon-o-envelope-open')
                    ->color('gray')
                    ->visible(fn (ContactMessage $record): bool => ! $record->isRead())
                    ->action(fn (ContactMessage $record) => $record->markAsRead()),

                Action::make('reply')
                    ->label('Reply')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('primary')
                    ->url(fn (ContactMessage $record): string => 'mailto:'.$record->email
                        .'?subject='.rawurlencode('Re: '.($record->subject ?? 'Your enquiry')))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markAsRead')
                        ->label('Mark as read')
                        ->icon('heroicon-o-envelope-open')
                        ->action(fn (Collection $records) => $records->each->markAsRead())
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No enquiries yet')
            ->emptyStateDescription('Messages sent through the contact form will appear here.');
    }
}
