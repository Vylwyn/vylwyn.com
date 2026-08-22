<?php

declare(strict_types=1);

namespace App\Filament\Resources\Technologies\Schemas;

use App\Enums\TechnologyCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class TechnologyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, ?string $state, callable $set): void {
                        if ($operation === 'create' && filled($state)) {
                            $set('slug', Str::slug($state));
                        }
                    }),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),

                Select::make('category')
                    ->options(TechnologyCategory::class)
                    ->required()
                    ->native(false),

                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->helperText('Order within its category. Lower first.'),

                Toggle::make('show_in_skills')
                    ->default(true)
                    ->columnSpanFull()
                    ->helperText('Off for project-only tags like "Offline sync" that would clutter the skills grid.'),
            ]);
    }
}
