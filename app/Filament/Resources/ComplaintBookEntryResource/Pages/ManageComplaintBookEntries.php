<?php

namespace App\Filament\Resources\ComplaintBookEntryResource\Pages;

use App\Filament\Resources\ComplaintBookEntryResource;
use Filament\Resources\Pages\ManageRecords;

class ManageComplaintBookEntries extends ManageRecords
{
    protected static string $resource = ComplaintBookEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
