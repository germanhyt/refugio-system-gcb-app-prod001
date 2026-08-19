<?php

namespace App\Filament\Resources\EventOfferResource\Pages;

use App\Filament\Actions\EditPageHeroAction;
use App\Filament\Resources\EventOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEventOffers extends ManageRecords
{
    protected static string $resource = EventOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditPageHeroAction::make(
                'hero_title_events',
                'Banner de /eventos',
                'hero_events',
            ),
            Actions\CreateAction::make(),
        ];
    }
}
