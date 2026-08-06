<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComplaintBookEntryResource\Pages;
use App\Models\ComplaintBookEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ComplaintBookEntryResource extends Resource
{
    protected static ?string $model = ComplaintBookEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Operaciones';

    protected static ?string $navigationLabel = 'Libro de reclamaciones';

    protected static ?string $modelLabel = 'Reclamación';

    protected static ?string $pluralModelLabel = 'Reclamaciones';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Consumidor')->schema([
                Forms\Components\TextInput::make('document_type')->label('Tipo documento')->disabled(),
                Forms\Components\TextInput::make('document_number')->label('N° documento')->disabled(),
                Forms\Components\TextInput::make('first_name')->label('Nombres')->disabled(),
                Forms\Components\TextInput::make('last_name')->label('Apellidos')->disabled(),
                Forms\Components\TextInput::make('department')->label('Departamento')->disabled(),
                Forms\Components\TextInput::make('address')->label('Dirección')->disabled(),
                Forms\Components\TextInput::make('phone')->label('Teléfono')->disabled(),
                Forms\Components\TextInput::make('email')->label('Correo')->disabled(),
                Forms\Components\TextInput::make('parent_name')->label('Padre/madre')->disabled(),
            ])->columns(2),
            Forms\Components\Section::make('Reclamación')->schema([
                Forms\Components\TextInput::make('claim_type')->label('Tipo')->disabled(),
                Forms\Components\TextInput::make('claimed_amount')->label('Monto')->disabled(),
                Forms\Components\Textarea::make('product_description')->label('Producto/servicio')->disabled()->columnSpanFull(),
                Forms\Components\Textarea::make('claim_detail')->label('Detalle')->disabled()->columnSpanFull(),
                Forms\Components\Textarea::make('consumer_request')->label('Pedido')->disabled()->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('claim_type')->label('Tipo')->badge()->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Nombre')
                    ->formatStateUsing(fn ($state, ComplaintBookEntry $record) => trim($record->first_name.' '.$record->last_name))
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('email')->label('Correo')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('phone')->label('Teléfono'),
                Tables\Columns\TextColumn::make('document_type')->label('Doc.')->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageComplaintBookEntries::route('/'),
        ];
    }
}
