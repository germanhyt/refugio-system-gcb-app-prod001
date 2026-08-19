<?php

namespace App\Filament\Actions;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Notifications\Notification;
use Livewire\Component;

class EditPageHeroAction
{
    public static function make(
        string $titleField,
        string $heading,
        string $mediaCollection,
    ): Action {
        return Action::make('editPageHero')
            ->label('Banner de la página')
            ->icon('heroicon-o-photo')
            ->color('gray')
            ->modalHeading($heading)
            ->modalDescription('Título e imagen del banner de esta página pública.')
            ->slideOver()
            ->form(self::fields($titleField, $mediaCollection))
            ->fillForm(function () use ($titleField, $mediaCollection): array {
                $settings = SiteSetting::current();

                return [
                    $titleField => $settings->{$titleField},
                    $mediaCollection => $settings->heroMediaFormState($mediaCollection),
                ];
            })
            ->action(function (array $data, Component $livewire) use ($titleField): void {
                $settings = SiteSetting::current();
                $settings
                    ->fill([$titleField => $data[$titleField] ?? null])
                    ->save();

                if (method_exists($livewire, 'getMountedActionForm')) {
                    $livewire->getMountedActionForm()?->model($settings)->saveRelationships();
                }

                Notification::make()
                    ->title('Banner actualizado')
                    ->success()
                    ->send();
            });
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    public static function fields(string $titleField, string $mediaCollection): array
    {
        return [
            Forms\Components\Textarea::make($titleField)
                ->label('Título del banner')
                ->helperText('Usa Enter para partir el título en dos líneas.')
                ->rows(2)
                ->maxLength(255)
                ->required()
                ->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make($mediaCollection)
                ->label('Imagen de fondo')
                ->collection($mediaCollection)
                ->model(fn () => SiteSetting::current())
                ->image()
                ->imageEditor()
                ->columnSpanFull(),
        ];
    }
}
