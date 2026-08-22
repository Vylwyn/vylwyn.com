<?php

declare(strict_types=1);

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Overview')
                    ->description('What this project is, in the words a recruiter will skim.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            /**
                             * Auto-fill the slug from the title, but only while creating.
                             * Changing a published project's slug breaks every existing
                             * link to it, so we never rewrite it on edit.
                             */
                            ->afterStateUpdated(function (string $operation, ?string $state, callable $set): void {
                                if ($operation === 'create' && filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Used in the URL: /work/your-slug'),

                        TextInput::make('tagline')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText('One line. Shown under the title on the project card.'),

                        Textarea::make('summary')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull()
                            ->helperText('The card paragraph. Aim for problem and outcome, not a feature list.'),
                    ]),

                Section::make('Case study')
                    ->description('Markdown. Problem, constraints, decisions, tradeoffs, outcome.')
                    ->collapsible()
                    ->schema([
                        MarkdownEditor::make('body')
                            ->columnSpanFull()
                            ->helperText('Leave empty until written — the site only links to case studies that exist.'),
                    ]),

                Section::make('Details')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->options(ProjectStatus::class)
                            ->default(ProjectStatus::InProgress)
                            ->required()
                            ->native(false),

                        TextInput::make('client')
                            ->maxLength(255)
                            ->helperText('Leave empty for personal projects.'),

                        TextInput::make('year')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue((int) date('Y') + 1)
                            ->helperText('Leave empty rather than guessing.'),

                        Select::make('technologies')
                            ->relationship('technologies', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Tags shown on the card. Managed under Technologies.'),
                    ]),

                Section::make('Links')
                    ->description('Only populated links are rendered on the site.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('live_url')->url()->maxLength(255)->label('Live site'),
                        TextInput::make('repo_url')->url()->maxLength(255)->label('Source repository'),
                        TextInput::make('app_store_url')->url()->maxLength(255)->label('App Store'),
                        TextInput::make('play_store_url')->url()->maxLength(255)->label('Google Play'),
                    ]),

                Section::make('Presentation')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('cover_image')
                            ->image()
                            ->imageEditor()
                            /**
                             * FILESYSTEM_DISK defaults to 'local', which lives outside
                             * public/ and is not web-accessible. Cover images must go
                             * to the 'public' disk, exposed via `artisan storage:link`.
                             */
                            ->disk('public')
                            ->directory('projects')
                            ->visibility('public')
                            ->columnSpanFull(),

                        Toggle::make('is_featured')
                            ->helperText('Featured projects appear on the homepage.'),

                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first.'),

                        DateTimePicker::make('published_at')
                            ->native(false)
                            ->columnSpanFull()
                            ->helperText('Empty means draft. A future date schedules publication.'),
                    ]),
            ]);
    }
}
