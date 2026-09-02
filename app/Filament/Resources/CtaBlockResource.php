<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CtaBlockResource\Pages;
use App\Models\CtaBlock;
use App\Support\SafePublicUrl;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CtaBlockResource extends Resource
{
    protected static ?string $model = CtaBlock::class;

    protected static ?string $navigationIcon = 'heroicon-o-cursor-arrow-rays';

    protected static ?string $navigationGroup = 'Contenido';

    protected static ?string $navigationLabel = 'CTAs';

    protected static ?string $modelLabel = 'CTA';

    protected static ?string $pluralModelLabel = 'CTAs';

    protected static ?int $navigationSort = 6;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')
                ->label('Tipo')
                ->options([
                    'evento' => 'Liderar evento',
                    'renta' => 'Rentar local',
                    'contacto' => 'Contactarnos',
                ])
                ->required()
                ->unique(ignoreRecord: true)
                ->disabled(fn (?CtaBlock $record) => $record !== null),
            Forms\Components\TextInput::make('title')
                ->label('Título')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('highlighted_word')
                ->label('Palabra destacada')
                ->maxLength(100),
            Forms\Components\Textarea::make('description')
                ->label('Descripción')
                ->rows(2)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('link_url')
                ->label('URL')
                ->helperText('Solo http(s), rutas internas (/) o anclas (#).')
                ->maxLength(500)
                ->rules(SafePublicUrl::RULE),
            Forms\Components\TextInput::make('link_text')
                ->label('Texto del enlace')
                ->maxLength(100),
            Forms\Components\Toggle::make('is_active')
                ->label('Activo')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'evento' => 'Evento',
                        'renta' => 'Renta',
                        'contacto' => 'Contacto',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('title')->label('Título')->searchable(),
                Tables\Columns\TextColumn::make('highlighted_word')->label('Destacado'),
                Tables\Columns\TextColumn::make('link_url')->label('URL')->limit(30),
                Tables\Columns\IconColumn::make('is_active')->label('Activo')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCtaBlocks::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return CtaBlock::query()->count() < 3;
    }
}
