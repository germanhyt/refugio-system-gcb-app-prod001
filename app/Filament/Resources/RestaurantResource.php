<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RestaurantResource\Pages;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
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

    protected static ?string $navigationGroup = 'Páginas';

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
                    Forms\Components\Textarea::make('short_description')
                        ->label('Descripción corta')
                        ->rows(3)
                        ->helperText('Texto principal de la ficha. Puede quedar vacía.')
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('description')
                        ->label('Descripción adicional')
                        ->helperText('Párrafos extra debajo de la descripción corta. Si replica el texto corto, la ficha no lo duplica.')
                        ->columnSpanFull(),
                ])->columns(2),
            Forms\Components\Section::make('Medios')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('logo')
                        ->label('Logo de marca')
                        ->helperText('Se muestra en la grilla (vista inicial), la cinta de logos y el panel.')
                        ->collection('logo')
                        ->image()
                        ->imageEditor(),
                    SpatieMediaLibraryFileUpload::make('banner_image')
                        ->label('Banner / cartel')
                        ->helperText('Cartel promocional del local. Se usa como fondo del hero en la ficha del restaurante.')
                        ->collection('banner_image')
                        ->image()
                        ->imageEditor(),
                    SpatieMediaLibraryFileUpload::make('featured_image')
                        ->label('Imagen de comida')
                        ->helperText('Plato para el hover de la grilla.')
                        ->collection('featured_image')
                        ->image()
                        ->imageEditor(),
                    SpatieMediaLibraryFileUpload::make('location_image')
                        ->label('Posición en el parque')
                        ->helperText('Plano del local dentro del Refugio (SVG o imagen). En la ficha alterna con el frontis al pasar el mouse.')
                        ->collection('location_image')
                        ->acceptedFileTypes(['image/svg+xml', 'image/png', 'image/jpeg', 'image/webp']),
                    SpatieMediaLibraryFileUpload::make('facade_image')
                        ->label('Foto de frontis')
                        ->helperText('Fachada del local. En la ficha se muestra al pasar el mouse sobre la posición en el parque.')
                        ->collection('facade_image')
                        ->image()
                        ->imageEditor(),
                    SpatieMediaLibraryFileUpload::make('exclusive_discount_image')
                        ->label('Imagen de descuentos exclusivos')
                        ->helperText('Se abre en un popup al pulsar «Descuentos exclusivos» en la ficha. Si está vacía, el indicador no es clicable.')
                        ->collection('exclusive_discount_image')
                        ->image()
                        ->imageEditor(),
                    SpatieMediaLibraryFileUpload::make('menu_pdf')
                        ->label('Menú PDF')
                        ->collection('menu_pdf')
                        ->acceptedFileTypes(['application/pdf']),
                ])->columns(2),
            Forms\Components\Section::make('Delivery')
                ->schema([
                    Forms\Components\Toggle::make('delivery_rappi_enabled')
                        ->label('Rappi habilitado')
                        ->default(true)
                        ->live(),
                    Forms\Components\TextInput::make('delivery_rappi_url')
                        ->label('URL Rappi')
                        ->url()
                        ->maxLength(500)
                        ->visible(fn (Forms\Get $get) => (bool) $get('delivery_rappi_enabled')),
                    Forms\Components\Toggle::make('delivery_peya_enabled')
                        ->label('PedidosYa habilitado')
                        ->default(true)
                        ->live(),
                    Forms\Components\TextInput::make('delivery_peya_url')
                        ->label('URL PedidosYa')
                        ->url()
                        ->maxLength(500)
                        ->visible(fn (Forms\Get $get) => (bool) $get('delivery_peya_enabled')),
                ])->columns(2),
            Forms\Components\Section::make('Redes y reservas')
                ->description('Enlaces de la ficha pública. Si un campo queda vacío, ese icono no se muestra.')
                ->schema([
                    Forms\Components\TextInput::make('website_url')
                        ->label('Página web')
                        ->url()
                        ->maxLength(500),
                    Forms\Components\TextInput::make('reservation_phone')
                        ->label('Reservas (teléfono)')
                        ->tel()
                        ->maxLength(30)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, ?string $state): void {
                            $digits = preg_replace('/\D+/', '', (string) $state);
                            if (strlen($digits) === 9) {
                                $set('whatsapp_url', 'https://wa.me/51'.$digits);
                            }
                        })
                        ->helperText('Se convierte en el WhatsApp de la ficha. Distinto del «¡Reserva aquí!» del header.'),
                    Forms\Components\TextInput::make('instagram_url')
                        ->label('Instagram')
                        ->url()
                        ->maxLength(500),
                    Forms\Components\TextInput::make('facebook_url')
                        ->label('Facebook')
                        ->url()
                        ->maxLength(500),
                    Forms\Components\TextInput::make('tiktok_url')
                        ->label('TikTok')
                        ->url()
                        ->maxLength(500),
                    Forms\Components\TextInput::make('whatsapp_url')
                        ->label('WhatsApp (enlace)')
                        ->helperText('Se completa solo si no hay teléfono de reservas. Si hay teléfono, se genera wa.me automáticamente.')
                        ->url()
                        ->maxLength(500),
                ])->columns(2),
            Forms\Components\Section::make('Descuentos exclusivos')
                ->description('En la ficha: indicador con visto. Si hay imagen, el indicador abre un popup. Los vencidos no aparecen; fechas futuras salen como «Próximamente».')
                ->schema([
                    Forms\Components\Radio::make('corporate_discount_mode')
                        ->label('Cómo se muestra')
                        ->options([
                            Restaurant::DISCOUNT_NONE => 'No mostrar',
                            Restaurant::DISCOUNT_BADGE => 'Solo indicador (visto bueno)',
                            Restaurant::DISCOUNT_DETAILS => 'Registrar detalles',
                        ])
                        ->default(Restaurant::DISCOUNT_NONE)
                        ->live()
                        ->columnSpanFull(),
                    Forms\Components\Repeater::make('corporate_discounts')
                        ->label('Descuentos')
                        ->visible(fn (Get $get): bool => $get('corporate_discount_mode') === Restaurant::DISCOUNT_DETAILS)
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('Título')
                                ->placeholder('Ej. Club El Comercio')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\Toggle::make('is_active')
                                ->label('Activo')
                                ->default(true),
                            Forms\Components\Textarea::make('description')
                                ->label('Detalle')
                                ->placeholder('Ej. 15% en toda la carta, comida y bebida.')
                                ->rows(2)
                                ->columnSpanFull(),
                            Forms\Components\DatePicker::make('starts_at')
                                ->label('Vigente desde')
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                            Forms\Components\DatePicker::make('ends_at')
                                ->label('Vigente hasta')
                                ->native(false)
                                ->displayFormat('d/m/Y'),
                        ])
                        ->columns(2)
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Descuento')
                        ->collapsed()
                        ->collapsible()
                        ->reorderable()
                        ->defaultItems(0)
                        ->addActionLabel('Agregar descuento')
                        ->columnSpanFull(),
                ]),
            Forms\Components\Section::make('Visibilidad y SEO')
                ->schema([
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
