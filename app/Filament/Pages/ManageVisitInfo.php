<?php

namespace App\Filament\Pages;

use App\Models\VisitInfo;
use Filament\Forms;
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

    protected static ?string $navigationLabel = 'Visítanos';

    protected static ?string $title = 'Visítanos / Contacto';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(VisitInfo::current()->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ubicación')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Dirección')
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
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('phone_events')
                            ->label('Tel. eventos')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                    ])->columns(3),
                Forms\Components\Section::make('Horarios')
                    ->schema([
                        Forms\Components\Repeater::make('schedule')
                            ->label('Horario')
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
                        Forms\Components\RichEditor::make('about_content')
                            ->label('Contenido Nosotros')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        VisitInfo::query()->updateOrCreate(['id' => 1], $this->form->getState());

        Notification::make()
            ->title('Visítanos actualizado')
            ->success()
            ->send();
    }
}
