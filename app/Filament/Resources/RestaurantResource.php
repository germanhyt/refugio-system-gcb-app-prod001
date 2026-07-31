<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RestaurantResource\Pages;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Str;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Contenido';

    protected static ?string $navigationLabel = 'Restaurantes';

    protected static ?string $modelLabel = 'Restaurante';

    protected static ?string $pluralModelLabel = 'Restaurantes';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Información')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\Select::make('categories')
                        ->label('Categorías')
                        ->relationship('categories', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')->required()->label('Nombre'),
                            Forms\Components\TextInput::make('slug')->required()->label('Slug'),
                        ]),
                    Forms\Components\RichEditor::make('description')
                        ->label('Descripción')
                        ->columnSpanFull(),
                ])->columns(2),
            Forms\Components\Section::make('Medios')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('logo')
                        ->label('Logo')
                        ->collection('logo')
                        ->image()
                        ->imageEditor(),
                    SpatieMediaLibraryFileUpload::make('featured_image')
                        ->label('Imagen destacada')
                        ->collection('featured_image')
                        ->image()
                        ->imageEditor(),
                    SpatieMediaLibraryFileUpload::make('menu_pdf')
                        ->label('Menú PDF')
                        ->collection('menu_pdf')
                        ->acceptedFileTypes(['application/pdf']),
                ])->columns(3),
            Forms\Components\Section::make('Contacto y SEO')
                ->schema([
                    Forms\Components\TextInput::make('whatsapp_url')
                        ->label('WhatsApp / Pedir')
                        ->url()
                        ->maxLength(500),
                    Forms\Components\TextInput::make('google_maps_url')
                        ->label('Google Maps')
                        ->url()
                        ->maxLength(500),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Orden')
                        ->numeric()
                        ->default(0),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Activo')
                        ->default(true),
                    Forms\Components\TextInput::make('meta_title')
                        ->label('Meta título')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('meta_description')
                        ->label('Meta descripción')
                        ->rows(2)
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('logo')
                    ->label('Logo')
                    ->collection('logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Categorías')
                    ->badge()
                    ->separator(','),
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
            ->filters([
                Tables\Filters\SelectFilter::make('categories')
                    ->label('Categoría')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRestaurants::route('/'),
            'create' => Pages\CreateRestaurant::route('/create'),
            'edit' => Pages\EditRestaurant::route('/{record}/edit'),
        ];
    }
}
