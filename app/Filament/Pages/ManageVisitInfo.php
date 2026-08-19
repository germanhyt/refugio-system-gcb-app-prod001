<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Models\VisitInfo;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageVisitInfo extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static string $view = 'filament.pages.manage-visit-info';

    protected static ?string $navigationGroup = 'Páginas';

    protected static ?string $navigationLabel = 'Nosotros';

    protected static ?string $title = 'Nosotros';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public ?VisitInfo $record = null;

    public function mount(): void
    {
        $this->record = VisitInfo::current();
        $settings = SiteSetting::current();
        $copy = $this->record->holaCopy();
        $this->form->fill([
            'about_eyebrow' => $this->record->about_eyebrow ?: VisitInfo::DEFAULT_HOLA_EYEBROW,
            'about_headline' => $copy['headline'],
            'about_body' => $copy['body'],
            'hero_title_about' => $settings->hero_title_about,
            'hero_about' => $settings->heroMediaFormState('hero_about'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Banner de la página')
                    ->description('Título e imagen del hero de /nosotros.')
                    ->schema([
                        Forms\Components\Textarea::make('hero_title_about')
                            ->label('Título del banner')
                            ->helperText('Usa Enter para partir el título en dos líneas (ej. ¿Quiénes / Somos?).')
                            ->rows(2)
                            ->maxLength(255)
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('hero_about')
                            ->label('Imagen de fondo')
                            ->helperText('Usa una foto panorámica (~1920×640). Las fotos verticales se recortan en este banner compacto.')
                            ->collection('hero_about')
                            ->model(fn () => SiteSetting::current())
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Sección «¡Hola! Somos Refugio Gastronómico»')
                    ->description('Saludo de la izquierda, titular y párrafo en /nosotros y en el home.')
                    ->schema([
                        Forms\Components\Textarea::make('about_eyebrow')
                            ->label('Título izquierdo')
                            ->helperText('Texto a la izquierda, p. ej. ¡Hola! Somos Refugio Gastronómico.')
                            ->rows(2)
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('about_headline')
                            ->label('Titular')
                            ->helperText('Texto en mayúsculas a la derecha, p. ej. TODO LO QUE TE PROVOCA, EN UN SOLO LUGAR.')
                            ->rows(2)
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('about_body')
                            ->label('Párrafo')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Carrusel «Nuestro espacio»')
                    ->description('Fotos del carrusel en /nosotros. La sección solo aparece cuando hay al menos una imagen.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('about_gallery')
                            ->label('Imágenes')
                            ->helperText('El orden de aquí es el del carrusel.')
                            ->collection('about_gallery')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $heroTitle = $data['hero_title_about'] ?? null;
        $headline = (string) ($data['about_headline'] ?? '');
        $body = (string) ($data['about_body'] ?? '');
        $eyebrow = (string) ($data['about_eyebrow'] ?? '');
        unset($data['hero_title_about'], $data['about_headline'], $data['about_body'], $data['about_eyebrow'], $data['hero_about']);

        $this->record->fill([
            ...$data,
            'about_eyebrow' => $eyebrow,
            'about_content' => VisitInfo::composeAboutContent($headline, $body),
        ])->save();

        SiteSetting::current()->fill(['hero_title_about' => $heroTitle])->save();
        $this->form->model($this->record)->saveRelationships();

        Notification::make()
            ->title('Nosotros actualizado')
            ->success()
            ->send();
    }
}
