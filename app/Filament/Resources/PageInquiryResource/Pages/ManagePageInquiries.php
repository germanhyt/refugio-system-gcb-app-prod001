<?php

namespace App\Filament\Resources\PageInquiryResource\Pages;

use App\Filament\Resources\PageInquiryResource;
use Filament\Resources\Pages\ManageRecords;

class ManagePageInquiries extends ManageRecords
{
    protected static string $resource = PageInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
