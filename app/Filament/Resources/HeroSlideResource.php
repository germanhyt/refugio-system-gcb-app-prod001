<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeroSlideResource\Pages;
use App\Models\HeroSlide;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HeroSlideResource extends Resource
{
    protected static ?string $model = HeroSlide::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Contenido';

    protected static ?string $navigationLabel = 'Banners / Hero';

    protected static ?string $modelLabel = 'Banner';

    protected static ?string $pluralModelLabel = 'Banners';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Contenido')
                    ->description('Soporta imagen o video. La imagen actúa como poster/fallback cuando el tipo es video. Controles del carousel solo aparecen con más de un slide activo.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('subtitle')
                            ->label('Subtítulo')
                            ->maxLength(500),
                        Forms\Components\Select::make('media_type')
                            ->label('Tipo de media')
                            ->options([
                                'image' => 'Imagen',
                                'video' => 'Video',
                            ])
                            ->default('image')
                            ->required()
                            ->live(),
                        Forms\Components\Textarea::make('description')
                            ->label('Notas / descripción')
                            ->rows(3)
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('background_image')
                            ->label(fn (Get $get) => $get('media_type') === 'video' ? 'Poster / fallback' : 'Imagen de fondo')
                            ->collection('background_image')
                            ->image()
                            ->imageEditor()
                            ->required(fn (Get $get) => $get('media_type') !== 'video')
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('background_video')
                            ->label('Video de fondo')
                            ->collection('background_video')
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime'])
                            ->visible(fn (Get $get) => $get('media_type') === 'video')
                            ->required(fn (Get $get) => $get('media_type') === 'video')
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('CTA y visibilidad')
                    ->schema([
                        Forms\Components\TextInput::make('cta_text')
                            ->label('Texto CTA')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('cta_url')
                            ->label('URL CTA')
                            ->url()
                            ->maxLength(500),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Activo')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('background_image')
                    ->label('Imagen')
                    ->collection('background_image'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('media_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state === 'video' ? 'Video' : 'Imagen'),
                Tables\Columns\TextColumn::make('subtitle')
                    ->label('Subtítulo')
                    ->limit(40)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListHeroSlides::route('/'),
            'create' => Pages\CreateHeroSlide::route('/create'),
            'edit' => Pages\EditHeroSlide::route('/{record}/edit'),
        ];
    }
}
