<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Páginas';

    protected static ?string $navigationLabel = 'Blog';

    protected static ?string $modelLabel = 'Post';

    protected static ?string $pluralModelLabel = 'Posts';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Artículo')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('author_handle')
                        ->label('Autor (@handle)')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('category')
                        ->label('Categoría')
                        ->default('Sin categoría')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('rating')
                        ->label('Rating')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(5)
                        ->step(0.1),
                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Publicado')
                        ->seconds(false),
                    Forms\Components\TextInput::make('external_url')
                        ->label('URL origen')
                        ->url()
                        ->maxLength(500)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Orden')
                        ->numeric()
                        ->default(0),
                    SpatieMediaLibraryFileUpload::make('featured_image')
                        ->label('Imagen destacada')
                        ->collection('featured_image')
                        ->image()
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('excerpt')
                        ->label('Extracto')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('body')
                        ->label('Contenido')
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'underline',
                            'bulletList',
                            'orderedList',
                            'blockquote',
                            'link',
                            'redo',
                            'undo',
                        ])
                        ->columnSpanFull(),
                    Forms\Components\Toggle::make('is_featured')->label('Destacado')->default(true),
                    Forms\Components\Toggle::make('is_active')->label('Activo')->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('featured_image')
                    ->collection('featured_image')
                    ->label('Img'),
                Tables\Columns\TextColumn::make('title')->label('Título')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('author_handle')->label('Autor'),
                Tables\Columns\TextColumn::make('rating')->label('★'),
                Tables\Columns\IconColumn::make('body')
                    ->label('Detalle')
                    ->boolean()
                    ->getStateUsing(fn (BlogPost $record): bool => filled($record->body)),
                Tables\Columns\IconColumn::make('is_featured')->label('Destacado')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Activo')->boolean(),
                Tables\Columns\TextColumn::make('published_at')->label('Fecha')->date('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Activo'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Destacado'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
