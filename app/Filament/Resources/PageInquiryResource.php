<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageInquiryResource\Pages;
use App\Models\PageInquiry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageInquiryResource extends Resource
{
    protected static ?string $model = PageInquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationGroup = 'Operaciones';

    protected static ?string $navigationLabel = 'Mensajes (convocatorias)';

    protected static ?string $modelLabel = 'Mensaje';

    protected static ?string $pluralModelLabel = 'Mensajes de convocatorias';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('page_slug')->label('Origen')->disabled(),
            Forms\Components\TextInput::make('full_name')->label('Nombre')->disabled(),
            Forms\Components\TextInput::make('email')->label('Email')->disabled(),
            Forms\Components\TextInput::make('phone')->label('Teléfono')->disabled(),
            Forms\Components\TextInput::make('company')->label('Empresa')->disabled(),
            Forms\Components\Textarea::make('message')->label('Mensaje')->disabled()->columnSpanFull()->rows(6),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('page_slug')
                    ->label('Origen')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'convocatorias' => 'Convocatorias',
                        default => (string) $state,
                    }),
                Tables\Columns\TextColumn::make('full_name')->label('Nombre')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('phone')->label('Teléfono')->toggleable(),
                Tables\Columns\TextColumn::make('message')->label('Mensaje')->limit(50),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePageInquiries::route('/'),
        ];
    }
}
