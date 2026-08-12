<?php

namespace App\Filament\Resources\ContactBlockResource\Pages;

use App\Filament\Resources\ContactBlockResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageContactBlocks extends ManageRecords
{
    protected static string $resource = ContactBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
