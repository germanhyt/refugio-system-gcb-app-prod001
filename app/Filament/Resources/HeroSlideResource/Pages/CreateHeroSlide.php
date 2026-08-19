<?php

namespace App\Filament\Resources\HeroSlideResource\Pages;

use App\Filament\Resources\HeroSlideResource;
use App\Models\HeroSlide;
use Filament\Resources\Pages\CreateRecord;

class CreateHeroSlide extends CreateRecord
{
    protected static string $resource = HeroSlideResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['title'] = trim((string) ($data['title'] ?? '')) ?: HeroSlide::DEFAULT_OVERLAY;
        $data['subtitle'] = null;
        $data['cta_text'] = null;
        $data['cta_url'] = null;

        return $data;
    }
}
