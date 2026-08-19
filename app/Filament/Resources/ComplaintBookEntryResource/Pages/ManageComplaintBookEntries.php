<?php

namespace App\Filament\Resources\ComplaintBookEntryResource\Pages;

use App\Filament\Actions\EditPageHeroAction;
use App\Filament\Resources\ComplaintBookEntryResource;
use App\Models\SiteSetting;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageComplaintBookEntries extends ManageRecords
{
    protected static string $resource = ComplaintBookEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditPageHeroAction::make(
                'hero_title_complaints',
                'Banner de /libro-de-reclamaciones',
                'hero_complaints',
            ),
            Actions\Action::make('editRecipients')
                ->label('Correos de notificación')
                ->icon('heroicon-o-envelope')
                ->color('gray')
                ->modalHeading('Correos del libro de reclamaciones')
                ->modalDescription('Cada nueva hoja se envía a estos destinatarios.')
                ->slideOver()
                ->form([
                    Forms\Components\TagsInput::make('complaint_book_recipients')
                        ->label('Correos')
                        ->placeholder('Agregar correo')
                        ->helperText('Pulsa Enter para agregar cada correo.')
                        ->required(),
                ])
                ->fillForm(fn (): array => [
                    'complaint_book_recipients' => SiteSetting::current()->complaintBookRecipients(),
                ])
                ->action(function (array $data): void {
                    $emails = array_values(array_filter(array_map(
                        static fn ($email): string => strtolower(trim((string) $email)),
                        $data['complaint_book_recipients'] ?? []
                    )));

                    $invalid = array_values(array_filter(
                        $emails,
                        static fn (string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) === false
                    ));

                    if ($emails === [] || $invalid !== []) {
                        Notification::make()
                            ->title($invalid !== [] ? 'Hay correos inválidos' : 'Agrega al menos un correo')
                            ->danger()
                            ->send();

                        return;
                    }

                    SiteSetting::current()
                        ->fill(['complaint_book_recipients' => $emails])
                        ->save();

                    Notification::make()
                        ->title('Correos actualizados')
                        ->success()
                        ->send();
                }),
        ];
    }
}
