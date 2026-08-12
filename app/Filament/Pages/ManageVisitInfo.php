<?php

namespace App\Filament\Pages;

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

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static string $view = 'filament.pages.manage-visit-info';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Nosotros / Visítanos';

    protected static ?string $title = 'Nosotros / Visítanos';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public ?VisitInfo $record = null;

    public function mount(): void
    {
        $this->record = VisitInfo::current();
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ubicación')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Dirección')
                            ->helperText('Card «¿Nos visitas?» (home, /nosotros, /eventos, /servicios) y datos de /contacto.')
                            ->required()
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('map_embed_url')
                            ->label('URL embed Google Maps')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('pedestrian_access')
                            ->label('Acceso peatonal')
                            ->rows(2),
                        Forms\Components\Textarea::make('vehicle_access')
                            ->label('Acceso vehicular')
                            ->rows(2),
                    ])->columns(2),
                Forms\Components\Section::make('Contacto')
                    ->schema([
                        Forms\Components\TextInput::make('phone_reservations')
                            ->label('Tel. reservas')
                            ->helperText('Página /contacto, junto al formulario.')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('phone_events')
                            ->label('Tel. eventos')
                            ->helperText('Página /contacto, junto al formulario.')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->helperText('Página /contacto, junto al formulario.')
                            ->email()
                            ->maxLength(255),
                    ])->columns(3),
                Forms\Components\Section::make('Horarios')
                    ->schema([
                        Forms\Components\Repeater::make('schedule')
                            ->label('Horario')
                            ->helperText('Lista de la card «¿Nos visitas?» en home y páginas internas.')
                            ->schema([
                                Forms\Components\TextInput::make('days')->label('Días')->required(),
                                Forms\Components\TextInput::make('hours')->label('Horas')->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->columnSpanFull(),
                        Forms\Components\TagsInput::make('amenities')
                            ->label('Amenidades')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Textos de Nosotros')
                    ->description('Titular y párrafo de «¡Hola! Somos Refugio Gastronómico» en /nosotros y en el home.')
                    ->schema([
                        Forms\Components\RichEditor::make('about_content')
                            ->label('Sección «¡Hola! Somos Refugio Gastronómico»')
                            ->helperText('Primer párrafo = titular derecho (ej. TODO LO QUE TE PROVOCA…). Segundo párrafo = texto descriptivo. Separa con una línea en blanco.')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Carrusel «Nuestro espacio»')
                    ->description('Lista de fotos del carrusel en /nosotros. Vacía por defecto: la sección solo aparece cuando hay al menos una imagen.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('about_gallery')
                            ->label('Imágenes')
                            ->helperText('Agrega JPG o PNG a esta lista. El orden de aquí es el del carrusel. Sin archivos, /nosotros no muestra «Nuestro espacio».')
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
        $this->record->fill($data)->save();
        $this->form->model($this->record)->saveRelationships();

        Notification::make()
            ->title('Visítanos actualizado')
            ->success()
            ->send();
    }
}
