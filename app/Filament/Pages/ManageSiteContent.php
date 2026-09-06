<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\SiteContent;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Edits the singleton SiteContent row.
 *
 * A Page rather than a Resource: resources are built around lists of records,
 * and a list containing exactly one row is a worse experience than a form.
 */
class ManageSiteContent extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static ?string $navigationLabel = 'Site content';

    protected static ?string $title = 'Site content';

    protected static ?int $navigationSort = 0;

    protected string $view = 'filament.pages.manage-site-content';

    /**
     * Form state. statePath('data') below binds the schema to this property.
     *
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteContent::current()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Hero')
                    ->description('The first screen. Everything above the buttons.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('hero_name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('hero_role')
                            ->label('Role line')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Shown in monospace under your name.'),

                        TextInput::make('hero_specialisms')
                            ->label('Specialisms')
                            ->maxLength(255)
                            ->helperText('Appended after an em dash, e.g. “Laravel · Flutter”.'),

                        TextInput::make('hero_tagline_lead')
                            ->label('Tagline — plain part')
                            ->required()
                            ->maxLength(255)
                            ->helperText('“I run the IT operations.”'),

                        TextInput::make('hero_tagline_highlight')
                            ->label('Tagline — gradient part')
                            ->maxLength(255)
                            ->helperText('“I also build the tools.” Rendered in the gradient.'),

                        Textarea::make('hero_lede')
                            ->label('Supporting paragraph')
                            ->rows(3)
                            ->columnSpanFull()
                            ->maxLength(500),
                    ]),

                Section::make('Section headings')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('work_heading')->label('Work heading')->required(),
                        Textarea::make('work_intro')->label('Work intro')->rows(2),
                        TextInput::make('experience_heading')->label('Experience heading')->required(),
                        Textarea::make('experience_intro')->label('Experience intro')->rows(2),
                    ]),

                Section::make('About')
                    ->description('Markdown. Blank lines separate paragraphs; **bold** for emphasis.')
                    ->schema([
                        TextInput::make('about_heading')
                            ->label('Heading')
                            ->required(),

                        FileUpload::make('photo')
                            ->label('Photo')
                            ->image()
                            ->avatar()
                            /**
                             * Cropped square on upload, so the About column can't
                             * be thrown out by a portrait or landscape original.
                             */
                            ->imageEditor()
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('600')
                            ->imageResizeTargetHeight('600')
                            ->disk('public')
                            ->directory('about')
                            ->visibility('public')
                            ->maxSize(4096)
                            ->helperText('Square. Leave empty to keep the placeholder.'),

                        MarkdownEditor::make('about_body')
                            ->label('Body')
                            ->columnSpanFull()
                            ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'undo', 'redo']),
                    ]),

                Section::make('Contact')
                    ->collapsible()
                    ->schema([
                        TextInput::make('contact_heading')->label('Heading')->required(),
                        Textarea::make('contact_intro')->label('Intro')->rows(2),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        /**
         * firstOrNew rather than create — this table holds exactly one row,
         * and saving twice must update it rather than add a second.
         */
        $content = SiteContent::query()->firstOrNew([]);
        $content->fill($data)->save();

        Notification::make()
            ->title('Site content updated')
            ->body('Changes are live immediately.')
            ->success()
            ->send();
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save changes')
                ->submit('save'),

            Action::make('view')
                ->label('View site')
                ->color('gray')
                ->url(route('home'))
                ->openUrlInNewTab(),
        ];
    }
}
