<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactBlockResource\Pages;
use App\Models\ContactBlock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactBlockResource extends Resource
{
    protected static ?string $model = ContactBlock::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Contenido';

    protected static ?string $navigationLabel = 'Contacto';

    protected static ?string $modelLabel = 'Bloque de contacto';

    protected static ?string $pluralModelLabel = 'Bloques de contacto';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label('Título')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('body')
                ->label('Cuerpo / subtítulo')
                ->rows(2)
                ->columnSpanFull(),
            Forms\Components\TagsInput::make('emails')
                ->label('Emails')
                ->placeholder('Agregar email'),
            Forms\Components\TagsInput::make('phones')
                ->label('Teléfonos')
                ->placeholder('Agregar teléfono'),
            Forms\Components\TextInput::make('sort_order')
                ->label('Orden')
                ->numeric()
                ->default(0)
                ->required(),
            Forms\Components\Toggle::make('is_active')
                ->label('Activo')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Título')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('body')->label('Cuerpo')->limit(40),
                Tables\Columns\IconColumn::make('is_active')->label('Activo')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Orden')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Activo'),
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
            'index' => Pages\ManageContactBlocks::route('/'),
        ];
    }
}
