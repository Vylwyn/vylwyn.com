<?php

declare(strict_types=1);

namespace App\Filament\Resources\Experiences\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExperienceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Role')
                    ->columns(2)
                    ->schema([
                        TextInput::make('role')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('organisation')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('location')
                            ->maxLength(255)
                            ->placeholder('Kuwait'),

                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first.'),
                    ]),

                Section::make('Dates')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('started_on')
                            ->required()
                            ->native(false)
                            ->displayFormat('M Y')
                            ->helperText('Use the first of the month.'),

                        DatePicker::make('ended_on')
                            ->native(false)
                            ->displayFormat('M Y')
                            ->after('started_on')
                            ->helperText('Leave empty for your current role — that is what marks it current.'),
                    ]),

                Section::make('Detail')
                    ->schema([
                        Textarea::make('summary')
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('Team size, systems owned, scale, one measurable outcome. Numbers beat adjectives.'),
                    ]),
            ]);
    }
}
