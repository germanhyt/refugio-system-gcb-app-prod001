<?php

namespace App\Filament\Resources\HomeRestaurantFeatureResource\Pages;

use App\Filament\Resources\HomeRestaurantFeatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHomeRestaurantFeatures extends ManageRecords
{
    protected static string $resource = HomeRestaurantFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
