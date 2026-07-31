<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.manage-site-settings';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Sitio';

    protected static ?string $title = 'Configuración del sitio';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public ?SiteSetting $record = null;

    public function mount(): void
    {
        $this->record = SiteSetting::current();
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Marca')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->label('Nombre del sitio')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slogan')
                            ->label('Slogan')
                            ->maxLength(255),
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->label('Logo')
                            ->collection('logo')
                            ->image(),
                        SpatieMediaLibraryFileUpload::make('favicon')
                            ->label('Favicon')
                            ->collection('favicon')
                            ->image(),
                        SpatieMediaLibraryFileUpload::make('og_image')
                            ->label('Imagen Open Graph')
                            ->collection('og_image')
                            ->image()
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('Redes y WhatsApp')
                    ->schema([
                        Forms\Components\TextInput::make('whatsapp_url')->label('WhatsApp')->url()->maxLength(500),
                        Forms\Components\TextInput::make('instagram_url')->label('Instagram')->url()->maxLength(500),
                        Forms\Components\TextInput::make('facebook_url')->label('Facebook')->url()->maxLength(500),
                        Forms\Components\TextInput::make('tiktok_url')->label('TikTok')->url()->maxLength(500),
                    ])->columns(2),
                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('Meta título')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('seo_description')
                            ->label('Meta descripción')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Secciones del sitio')
                    ->description('Controla qué bloques aparecen en el frontend público.')
                    ->schema([
                        Forms\Components\Toggle::make('show_blog_section')
                            ->label('Mostrar sección Blog (home y /blog)')
                            ->helperText('Si está desactivado, se oculta el bloque de foodies en home y las rutas /blog responden 404.')
                            ->default(true)
                            ->inline(false),
                    ]),
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $this->record->fill($data)->save();
        $this->form->model($this->record)->saveRelationships();

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }
}
