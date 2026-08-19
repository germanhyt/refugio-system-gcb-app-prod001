<?php

namespace App\Filament\Resources\StaticPageResource\Pages;

use App\Filament\Resources\StaticPageResource;
use App\Models\SiteSetting;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaticPage extends EditRecord
{
    protected static string $resource = StaticPageResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['blocks'] = collect($data['blocks'] ?? [])
            ->map(function (array $block): array {
                if (($block['type'] ?? '') === 'list') {
                    $block['list_items'] = $block['items'] ?? [];
                }

                return $block;
            })
            ->all();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['blocks'] = collect($data['blocks'] ?? [])
            ->map(function (array $block): array {
                if (($block['type'] ?? '') === 'list') {
                    $block['items'] = array_values($block['list_items'] ?? []);
                    unset($block['list_items']);
                }

                return $block;
            })
            ->all();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view')
                ->label(fn (): string => $this->record->isDocumentRedirect() ? 'Ver PDF' : 'Ver página')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(function (): ?string {
                    if ($this->record->isDocumentRedirect()) {
                        return SiteSetting::current()->ulimaDiscountsPdfUrl() ?: $this->record->routeUrl();
                    }

                    return $this->record->routeUrl();
                })
                ->openUrlInNewTab()
                ->visible(fn (): bool => filled($this->record->routeUrl()) || filled(SiteSetting::current()->ulimaDiscountsPdfUrl())),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return StaticPageResource::getUrl('index');
    }
}
