<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListContactMessages extends ListRecords
{
    protected static string $resource = ContactMessageResource::class;

    /**
     * No CreateAction — enquiries come from the public form, not from here.
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('markAllRead')
                ->label('Mark all read')
                ->icon('heroicon-o-envelope-open')
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn (): bool => ContactMessage::query()->unread()->exists())
                ->action(function (): void {
                    /**
                     * A single UPDATE rather than loading every row and saving
                     * each one. Irrelevant at ten messages, correct at ten thousand.
                     */
                    ContactMessage::query()->unread()->update(['read_at' => now()]);
                }),
        ];
    }
}
