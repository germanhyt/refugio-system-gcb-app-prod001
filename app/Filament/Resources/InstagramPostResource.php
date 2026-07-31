<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstagramPostResource\Pages;
use App\Models\InstagramPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class InstagramPostResource extends Resource
{
    protected static ?string $model = InstagramPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationGroup = 'Contenido';

    protected static ?string $navigationLabel = 'Instagram';

    protected static ?string $modelLabel = 'Post IG';

    protected static ?string $pluralModelLabel = 'Posts IG';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('permalink')
                ->label('Permalink IG')
                ->required()
                ->url()
                ->maxLength(500),
            Forms\Components\Select::make('media_type')
                ->label('Tipo')
                ->options([
                    'image' => 'Imagen',
                    'reel' => 'Reel',
                    'video' => 'Video',
                ])
                ->default('image')
                ->required(),
            Forms\Components\TextInput::make('likes_count')->label('Likes')->numeric()->default(0),
            Forms\Components\TextInput::make('comments_count')->label('Comentarios')->numeric()->default(0),
            Forms\Components\TextInput::make('sort_order')->label('Orden')->numeric()->default(0),
            Forms\Components\Textarea::make('caption')->label('Caption')->rows(4)->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('image')
                ->label('Imagen')
                ->collection('image')
                ->image()
                ->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')->label('Activo')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                    ->collection('image')
                    ->label('Img'),
                Tables\Columns\TextColumn::make('permalink')->label('Link')->limit(35),
                Tables\Columns\TextColumn::make('media_type')->label('Tipo')->badge(),
                Tables\Columns\TextColumn::make('likes_count')->label('♥'),
                Tables\Columns\IconColumn::make('is_active')->label('Activo')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInstagramPosts::route('/'),
        ];
    }
}
