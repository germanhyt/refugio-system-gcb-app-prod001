<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeRestaurantFeatureResource\Pages;
use App\Models\HomeRestaurantFeature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomeRestaurantFeatureResource extends Resource
{
    protected static ?string $model = HomeRestaurantFeature::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Inicio';

    protected static ?string $navigationLabel = 'Cinta de Logos (Home)';

    protected static ?string $modelLabel = 'Logo en cinta';

    protected static ?string $pluralModelLabel = 'Cinta de logos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Cinta de logos')
                ->description('Logos que rotan en la cinta «Nuestros restaurantes» del home. El logo se toma del restaurante seleccionado.')
                ->schema([
                    Forms\Components\Select::make('restaurant_id')
                        ->label('Restaurante')
                        ->relationship('restaurant', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->unique(ignoreRecord: true),
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
                Tables\Columns\TextColumn::make('restaurant.name')
                    ->label('Restaurante')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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
            'index' => Pages\ManageHomeRestaurantFeatures::route('/'),
        ];
    }
}
