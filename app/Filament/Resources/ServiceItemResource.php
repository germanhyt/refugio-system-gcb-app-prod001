<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceItemResource\Pages;
use App\Models\ServiceItem;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceItemResource extends Resource
{
    protected static ?string $model = ServiceItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Páginas';

    protected static ?string $navigationLabel = 'Servicios';

    protected static ?string $modelLabel = 'Servicio';

    protected static ?string $pluralModelLabel = 'Servicios';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label('Título')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('icon_key')
                ->label('Icono por defecto')
                ->helperText('Se usa en la grilla si no subes una imagen. WhatsApp, parking, delivery, etc.')
                ->options(ServiceItem::iconKeyOptions())
                ->searchable()
                ->nullable(),
            Forms\Components\Textarea::make('description')
                ->label('Descripción')
                ->helperText('Texto bajo el título. Si hay un teléfono configurado, el número se convierte en enlace de WhatsApp.')
                ->rows(2)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('contact_phone')
                ->label('Teléfono / WhatsApp')
                ->helperText('Ej. 991 318 720. Si también está en la descripción, se enlaza ahí mismo. Si no, se agrega al final.')
                ->tel()
                ->maxLength(40),
            Forms\Components\TextInput::make('whatsapp_message')
                ->label('Mensaje de WhatsApp')
                ->helperText('Texto precargado al abrir el chat. Si queda vacío: “Hola Refugio… quiero información sobre [servicio]”.')
                ->maxLength(255)
                ->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('icon')
                ->label('Icono (imagen, opcional)')
                ->helperText('Si subes una imagen, reemplaza el icono por defecto de arriba.')
                ->collection('icon')
                ->image()
                ->columnSpanFull(),
            Forms\Components\TextInput::make('sort_order')
                ->label('Orden')
                ->numeric()
                ->default(0)
                ->required(),
            Forms\Components\Toggle::make('show_on_home')
                ->label('Mostrar en home')
                ->default(false),
            Forms\Components\Toggle::make('is_active')
                ->label('Activo')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('icon')
                    ->label('Icono')
                    ->collection('icon'),
                Tables\Columns\TextColumn::make('title')->label('Título')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contact_phone')
                    ->label('WhatsApp')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('icon_key')
                    ->label('Icono')
                    ->formatStateUsing(fn (?string $state): string => ServiceItem::iconKeyOptions()[$state] ?? ($state ?: '—'))
                    ->toggleable(),
                Tables\Columns\IconColumn::make('show_on_home')->label('Home')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Activo')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Orden')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Activo'),
                Tables\Filters\TernaryFilter::make('show_on_home')->label('En home'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageServiceItems::route('/'),
        ];
    }
}
