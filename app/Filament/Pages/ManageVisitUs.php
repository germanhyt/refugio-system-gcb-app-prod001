<?php

namespace App\Filament\Pages;

use App\Models\VisitInfo;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageVisitUs extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static string $view = 'filament.pages.manage-visit-us';

    protected static ?string $navigationGroup = 'Inicio';

    protected static ?string $navigationLabel = '¿Nos visitas?';

    protected static ?string $title = '¿Nos visitas?';

    protected static ?int $navigationSort = 4;

    public ?array $data = [];

    public ?VisitInfo $record = null;

    public function mount(): void
    {
        $this->record = VisitInfo::current();
        $this->form->fill([
            'address' => $this->record->address,
            'map_embed_url' => $this->record->mapEmbedUrl(),
            'schedule' => $this->record->schedule,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Componente reutilizable')
                    ->description('Esta card se muestra en home, /nosotros, /eventos y /servicios. Un solo lugar para editarla.')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Dirección')
                            ->required()
                            ->maxLength(500)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('map_embed_url')
                            ->label('URL de Google Maps')
                            ->helperText('Pega la URL de embed o el iframe completo; se extrae el src automáticamente. Si lo dejas vacío se usa el mapa de Refugio.')
                            ->placeholder(VisitInfo::DEFAULT_MAP_EMBED_URL)
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('schedule')
                            ->label('Horarios')
                            ->schema([
                                Forms\Components\TextInput::make('days')->label('Días')->required(),
                                Forms\Components\TextInput::make('hours')->label('Horas')->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $data['map_embed_url'] = VisitInfo::normalizeMapEmbedUrl($data['map_embed_url'] ?? null);
        $this->record->fill($data)->save();
        $this->form->fill([
            'address' => $this->record->address,
            'map_embed_url' => $this->record->mapEmbedUrl(),
            'schedule' => $this->record->schedule,
        ]);

        Notification::make()
            ->title('¿Nos visitas? actualizado')
            ->success()
            ->send();
    }
}
