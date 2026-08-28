<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactMessages\Pages;

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    /**
     * Opening an enquiry marks it read — the same behaviour as any mail client.
     * Doing it here rather than requiring a button means the unread badge stays
     * meaningful without you having to maintain it by hand.
     */
    public function mount(int|string $record): void
    {
        parent::mount($record);

        /** @var ContactMessage $message */
        $message = $this->getRecord();

        if (! $message->isRead()) {
            $message->markAsRead();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->label('Reply by email')
                ->icon('heroicon-o-arrow-uturn-left')
                ->url(fn (ContactMessage $record): string => 'mailto:'.$record->email
                    .'?subject='.rawurlencode('Re: '.($record->subject ?? 'Your enquiry')))
                ->openUrlInNewTab(),

            Action::make('markAsUnread')
                ->label('Mark unread')
                ->icon('heroicon-o-envelope')
                ->color('gray')
                ->visible(fn (ContactMessage $record): bool => $record->isRead())
                ->action(function (ContactMessage $record): void {
                    $record->update(['read_at' => null]);
                }),

            DeleteAction::make(),
        ];
    }
}
