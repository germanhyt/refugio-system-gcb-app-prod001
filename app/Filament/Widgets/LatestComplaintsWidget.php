<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ComplaintBookEntryResource;
use App\Models\ComplaintBookEntry;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestComplaintsWidget extends BaseWidget
{
    protected static ?string $heading = 'Últimas reclamaciones';

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(ComplaintBookEntry::query()->latest())
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('claim_type')
                    ->label('Tipo')
                    ->badge(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Nombre')
                    ->formatStateUsing(fn ($state, ComplaintBookEntry $record) => trim($record->first_name.' '.$record->last_name)),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo'),
            ])
            ->paginated([5])
            ->defaultPaginationPageOption(5)
            ->recordUrl(fn (ComplaintBookEntry $record): string => ComplaintBookEntryResource::getUrl('index'))
            ->emptyStateHeading('Aún no hay hojas registradas')
            ->emptyStateDescription('Las nuevas reclamaciones del sitio aparecerán aquí.');
    }
}
