<?php

namespace App\Filament\Resources\RestaurantResource\Pages;

use App\Filament\Actions\EditPageHeroAction;
use App\Filament\Resources\RestaurantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRestaurants extends ListRecords
{
    protected static string $resource = RestaurantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditPageHeroAction::make(
                'hero_title_restaurants',
                'Banner de /restaurantes',
                'hero_restaurants',
            ),
            Actions\CreateAction::make(),
        ];
    }
}
