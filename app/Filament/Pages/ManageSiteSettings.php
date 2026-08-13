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
                            ->helperText('No se muestra en la web actual: la sección de slogan/hotspots del home ya no está activa.')
                            ->maxLength(255),
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->label('Logo')
                            ->helperText('Logo del header sobre el banner (hero). Si está vacío, se usa logo-v1-base.')
                            ->collection('logo')
                            ->image(),
                        SpatieMediaLibraryFileUpload::make('favicon')
                            ->label('Favicon')
                            ->helperText('Icono de pestaña del navegador y del panel admin.')
                            ->collection('favicon')
                            ->image(),
                        SpatieMediaLibraryFileUpload::make('og_image')
                            ->label('Imagen Open Graph')
                            ->helperText('Imagen al compartir el sitio en redes (WhatsApp, Facebook, etc.). Si está vacía, se usa el logo.')
                            ->collection('og_image')
                            ->image()
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('Redes y WhatsApp')
                    ->description('Enlaces que alimentan el header, el menú hamburguesa y el footer. Si un campo queda vacío, ese icono no se muestra (WhatsApp, Instagram y Facebook tienen un fallback).')
                    ->schema([
                        Forms\Components\TextInput::make('whatsapp_url')
                            ->label('WhatsApp')
                            ->helperText('Botón «¡Reserva aquí!» del header (sobre el banner). También el botón Reservar en el detalle de un evento con fecha.')
                            ->url()
                            ->maxLength(500),
                        Forms\Components\TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->helperText('Icono en el menú overlay (hamburguesa) y en el footer, columna Legal.')
                            ->url()
                            ->maxLength(500),
                        Forms\Components\TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->helperText('Icono en el menú overlay (hamburguesa) y en el footer, columna Legal.')
                            ->url()
                            ->maxLength(500),
                        Forms\Components\TextInput::make('tiktok_url')
                            ->label('TikTok')
                            ->helperText('Icono en el menú overlay y en el footer. Si está vacío, no se muestra.')
                            ->url()
                            ->maxLength(500),
                        Forms\Components\TextInput::make('youtube_url')
                            ->label('YouTube')
                            ->helperText('Icono en el menú overlay y en el footer. Si está vacío, no se muestra.')
                            ->url()
                            ->maxLength(500),
                    ])->columns(2),
                Forms\Components\Section::make('Documentos')
                    ->description('Archivos que se abren desde el footer.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('ulima_discounts_pdf')
                            ->label('PDF Descuentos U. Lima')
                            ->helperText('Si se carga, el enlace «Descuentos U. Lima» del footer abre este PDF en una ventana nueva.')
                            ->collection('ulima_discounts_pdf')
                            ->acceptedFileTypes(['application/pdf'])
                            ->columnSpanFull(),
                    ]),
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
                Forms\Components\Section::make('Títulos de hero')
                    ->description('Textos principales de las páginas Nosotros, Restaurantes, Eventos y Servicios.')
                    ->schema([
                        Forms\Components\Textarea::make('hero_title_about')
                            ->label('Nosotros')
                            ->helperText('Usa Enter para partir el título en dos líneas (ej. ¿Quiénes / Somos?).')
                            ->rows(2)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hero_title_restaurants')
                            ->label('Restaurantes')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hero_title_events')
                            ->label('Eventos')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('hero_title_services')
                            ->label('Servicios')
                            ->maxLength(255),
                    ])->columns(2),
                Forms\Components\Section::make('Fondos de banner')
                    ->description('Tres fondos según el título de cada página: Restaurantes, Servicios y Eventos.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('hero_restaurants')
                            ->label('Restaurantes — ¿Qué te provoca hoy?')
                            ->collection('hero_restaurants')
                            ->image()
                            ->imageEditor(),
                        SpatieMediaLibraryFileUpload::make('hero_services')
                            ->label('Servicios — Nuestros servicios')
                            ->collection('hero_services')
                            ->image()
                            ->imageEditor(),
                        SpatieMediaLibraryFileUpload::make('hero_events')
                            ->label('Eventos — Somos el refugio de tu diversión')
                            ->collection('hero_events')
                            ->image()
                            ->imageEditor(),
                    ])->columns(3),
                Forms\Components\Section::make('Secciones del sitio')
                    ->description('Controla qué bloques aparecen en el frontend público.')
                    ->schema([
                        Forms\Components\Toggle::make('show_blog_section')
                            ->label('Habilitar rutas /blog')
                            ->helperText('El blog ya no aparece en home. Este toggle controla si /blog responde o devuelve 404. El enlace del footer sigue visible.')
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
