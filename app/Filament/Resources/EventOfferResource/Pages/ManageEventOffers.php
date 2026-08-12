<?php

namespace App\Filament\Resources\EventOfferResource\Pages;

use App\Filament\Resources\EventOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEventOffers extends ManageRecords
{
    protected static string $resource = EventOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
