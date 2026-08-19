<?php

namespace App\Filament\Resources\HeroSlideResource\Pages;

use App\Filament\Resources\HeroSlideResource;
use App\Models\HeroSlide;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHeroSlide extends EditRecord
{
    protected static string $resource = HeroSlideResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['title'] = trim((string) ($data['title'] ?? '')) ?: HeroSlide::DEFAULT_OVERLAY;
        $data['subtitle'] = null;
        $data['cta_text'] = null;
        $data['cta_url'] = null;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
