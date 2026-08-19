<?php

namespace App\Filament\Resources\ServiceItemResource\Pages;

use App\Filament\Actions\EditPageHeroAction;
use App\Filament\Resources\ServiceItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageServiceItems extends ManageRecords
{
    protected static string $resource = ServiceItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditPageHeroAction::make(
                'hero_title_services',
                'Banner de /servicios',
                'hero_services',
            ),
            Actions\CreateAction::make(),
        ];
    }
}
