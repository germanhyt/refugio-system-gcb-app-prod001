<?php

namespace App\Filament\Resources\CtaBlockResource\Pages;

use App\Filament\Resources\CtaBlockResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCtaBlocks extends ManageRecords
{
    protected static string $resource = CtaBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
