<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactMessages\Schemas;

use App\Models\ContactMessage;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Message')
                    ->schema([
                        TextEntry::make('subject')
                            ->placeholder('No subject')
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('message')
                            ->label('')
                            ->prose()
                            ->columnSpanFull(),
                    ]),

                Section::make('Sender')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name'),

                        TextEntry::make('email')
                            ->label('Email address')
                            ->copyable()
                            ->copyMessage('Copied')
                            /** One click to reply, with the subject prefilled. */
                            ->url(fn (ContactMessage $record): string => 'mailto:'.$record->email
                                .'?subject='.rawurlencode('Re: '.($record->subject ?? 'Your enquiry')))
                            ->color('primary'),

                        TextEntry::make('created_at')
                            ->label('Received')
                            ->dateTime('j M Y, H:i')
                            ->hint(fn (ContactMessage $record): string => $record->created_at->diffForHumans()),

                        TextEntry::make('read_at')
                            ->label('Read')
                            ->dateTime('j M Y, H:i')
                            ->placeholder('Unread'),
                    ]),

                Section::make('Delivery & diagnostics')
                    ->description('Only useful when chasing a problem.')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        IconEntry::make('notified')
                            ->label('Notification emailed')
                            ->boolean()
                            ->trueColor('success')
                            ->falseColor('danger')
                            ->helperText(fn (ContactMessage $record): string => $record->notified
                                ? 'The alert email was sent successfully.'
                                : 'The email failed. The message is stored safely — check your mail configuration.'),

                        TextEntry::make('ip_address')
                            ->label('IP address')
                            ->placeholder('Not recorded'),

                        TextEntry::make('user_agent')
                            ->label('Browser')
                            ->placeholder('Not recorded')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
