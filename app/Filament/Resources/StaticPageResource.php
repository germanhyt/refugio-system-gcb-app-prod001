<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaticPageResource\Pages;
use App\Models\StaticPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StaticPageResource extends Resource
{
    protected static ?string $model = StaticPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Contenido';

    protected static ?string $navigationLabel = 'Páginas informativas';

    protected static ?string $modelLabel = 'Página informativa';

    protected static ?string $pluralModelLabel = 'Páginas informativas';

    protected static ?int $navigationSort = 9;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Encabezado')
                ->schema([
                    Forms\Components\TextInput::make('slug')
                        ->label('Identificador')
                        ->disabled()
                        ->dehydrated(false),
                    Forms\Components\TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('intro')
                        ->label('Introducción')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),
            Forms\Components\Section::make('Contenido')
                ->description('Bloques que se muestran debajo del hero en la página pública.')
                ->schema([
                    Forms\Components\Repeater::make('blocks')
                        ->label('Bloques')
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->label('Tipo')
                                ->options([
                                    'highlight' => 'Destacado',
                                    'list' => 'Lista con viñetas',
                                    'faq' => 'Preguntas y respuestas',
                                    'cards' => 'Tarjetas',
                                ])
                                ->required()
                                ->live(),
                            Forms\Components\TextInput::make('title')
                                ->label('Título')
                                ->visible(fn (Get $get): bool => in_array($get('type'), ['highlight', 'list'], true))
                                ->required(fn (Get $get): bool => in_array($get('type'), ['highlight', 'list'], true))
                                ->maxLength(255),
                            Forms\Components\Textarea::make('body')
                                ->label('Texto')
                                ->rows(4)
                                ->visible(fn (Get $get): bool => $get('type') === 'highlight')
                                ->required(fn (Get $get): bool => $get('type') === 'highlight')
                                ->columnSpanFull(),
                            Forms\Components\TagsInput::make('list_items')
                                ->label('Ítems')
                                ->placeholder('Agregar ítem')
                                ->visible(fn (Get $get): bool => $get('type') === 'list')
                                ->columnSpanFull(),
                            Forms\Components\Repeater::make('items')
                                ->label(fn (Get $get): string => match ($get('type')) {
                                    'faq' => 'Preguntas',
                                    'cards' => 'Tarjetas',
                                    default => 'Ítems',
                                })
                                ->schema(fn (Get $get): array => match ($get('type')) {
                                    'faq' => [
                                        Forms\Components\TextInput::make('question')
                                            ->label('Pregunta')
                                            ->required()
                                            ->maxLength(500),
                                        Forms\Components\Textarea::make('answer')
                                            ->label('Respuesta')
                                            ->required()
                                            ->rows(3),
                                    ],
                                    'cards' => [
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('body')
                                            ->label('Texto')
                                            ->required()
                                            ->rows(3),
                                    ],
                                    default => [],
                                })
                                ->visible(fn (Get $get): bool => in_array($get('type'), ['faq', 'cards'], true))
                                ->defaultItems(1)
                                ->columnSpanFull(),
                        ])
                        ->collapsible()
                        ->cloneable()
                        ->reorderable()
                        ->defaultItems(1)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Página')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Ruta')
                    ->formatStateUsing(fn (string $state, StaticPage $record): string => ltrim(parse_url((string) $record->routeUrl(), PHP_URL_PATH) ?: '', '/'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('slug')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaticPages::route('/'),
            'edit' => Pages\EditStaticPage::route('/{record}/edit'),
        ];
    }
}
