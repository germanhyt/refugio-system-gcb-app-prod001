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

    protected static ?string $navigationGroup = 'Inicio';

    protected static ?string $navigationLabel = 'Banner (Home)';

    protected static ?string $modelLabel = 'Slide de banner';

    protected static ?string $pluralModelLabel = 'Banner (Home)';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Media')
                    ->description('Imagen o video de fondo. Los controles laterales del carousel solo aparecen si hay más de un slide activo.')
                    ->schema([
                        Forms\Components\Select::make('media_type')
                            ->label('Tipo de media')
                            ->options([
                                'image' => 'Imagen',
                                'video' => 'Video',
                            ])
                            ->default('image')
                            ->required()
                            ->live(),
                        SpatieMediaLibraryFileUpload::make('background_image')
                            ->label(fn (Get $get) => $get('media_type') === 'video' ? 'Poster (opcional)' : 'Imagen de fondo')
                            ->helperText(fn (Get $get) => $get('media_type') === 'video'
                                ? 'Imagen que se muestra mientras carga el video. Si no subes una, el video arranca sin poster.'
                                : null)
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
                    ]),
                Forms\Components\Section::make('Texto overlay')
                    ->description('Texto grande sobre el banner, como «¡DE TODO, PARA TODOS!». Sin título, subtítulo ni botones CTA.')
                    ->schema([
                        Forms\Components\Textarea::make('title')
                            ->label('Texto overlay')
                            ->helperText('Texto grande sobre el video o la imagen.')
                            ->placeholder(HeroSlide::DEFAULT_OVERLAY)
                            ->default(HeroSlide::DEFAULT_OVERLAY)
                            ->rows(3)
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Visibilidad')
                    ->schema([
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
                    ->label('Media')
                    ->collection('background_image'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Texto overlay')
                    ->placeholder('Sin texto')
                    ->limit(40)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('media_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state === 'video' ? 'Video' : 'Imagen'),
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
