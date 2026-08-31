<?php

namespace App\Filament\Pages;

use App\Mail\SiteMailTest;
use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ManageSiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.manage-site-settings';

    protected static ?string $navigationGroup = 'Configuración';

    protected static ?string $navigationLabel = 'Configuraciones del sitio';

    protected static ?string $title = 'Configuraciones del sitio';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    public ?SiteSetting $record = null;

    public ?string $mailPreviewHtml = null;

    public ?string $mailPreviewTo = null;

    public function mount(): void
    {
        $this->record = SiteSetting::current();
        $values = $this->record->attributesToArray();
        unset($values['mail_password']);
        $values['mail_mailer'] = $this->record->mail_mailer ?: (app()->environment('local') ? 'log' : 'smtp');
        $this->form->fill($values);
    }

    /**
     * @return array<int, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendTestMail')
                ->label('Enviar correo de prueba')
                ->icon('heroicon-o-paper-airplane')
                ->color('gray')
                ->modalHeading('Simular envío de correo')
                ->modalDescription('Simula el envío con el emisor de marketing. No usa SMTP ni sale a internet.')
                ->modalSubmitActionLabel('Enviar')
                ->form([
                    Forms\Components\TextInput::make('email')
                        ->label('Destinatario')
                        ->email()
                        ->required()
                        ->default(fn (): ?string => auth()->user()?->email),
                ])
                ->action(function (array $data): void {
                    $this->deliverTestMail((string) $data['email']);
                }),
        ];
    }

    public function deliverTestMail(string $email): void
    {
        $this->record = SiteSetting::current();
        $this->record->applyMailConfig();

        $mailable = new SiteMailTest($email);

        try {
            Mail::mailer('log')->to($email)->send($mailable);
        } catch (Throwable $exception) {
            Notification::make()
                ->title('No se pudo simular el correo')
                ->body($exception->getMessage())
                ->danger()
                ->send();

            return;
        }

        $this->mailPreviewTo = $email;
        $this->mailPreviewHtml = $mailable->render();
        file_put_contents(storage_path('app/mail-preview.html'), $this->mailPreviewHtml);

        Notification::make()
            ->title('Correo simulado')
            ->body("No salió a internet. Destinatario: {$email}. Vista previa abajo.")
            ->success()
            ->send();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Marca')
                    ->schema([
                        Forms\Components\TextInput::make('site_name')
                            ->label('Nombre del sitio')
                            ->required()
                            ->maxLength(255),
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->label('Logo')
                            ->helperText('Logo del header sobre el banner (hero).')
                            ->collection('logo')
                            ->image(),
                        SpatieMediaLibraryFileUpload::make('favicon')
                            ->label('Favicon')
                            ->helperText('Icono de pestaña del navegador y del panel admin.')
                            ->collection('favicon')
                            ->image(),
                        SpatieMediaLibraryFileUpload::make('og_image')
                            ->label('Imagen Open Graph')
                            ->helperText('Imagen al compartir el sitio en redes (WhatsApp, Facebook, etc.). Si está vacía, se usa el logo.')
                            ->collection('og_image')
                            ->image()
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('Redes y WhatsApp')
                    ->description('Alimentan la barra flotante de la derecha, el menú hamburguesa y el footer. Si un campo queda vacío, ese icono no aparece.')
                    ->schema([
                        Forms\Components\Toggle::make('show_fixed_social')
                            ->label('Mostrar barra flotante a la derecha')
                            ->helperText('Iconos fijos de redes en el borde derecho de toda la web.')
                            ->default(true)
                            ->inline(false)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('whatsapp_url')
                            ->label('WhatsApp')
                            ->helperText('Botón «¡Reserva aquí!» del header, icono flotante de WhatsApp y reservas de eventos.')
                            ->url()
                            ->maxLength(500),
                        Forms\Components\TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->helperText('Barra flotante, menú hamburguesa y footer.')
                            ->url()
                            ->maxLength(500),
                        Forms\Components\TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->helperText('Barra flotante, menú hamburguesa y footer.')
                            ->url()
                            ->maxLength(500),
                        Forms\Components\TextInput::make('tiktok_url')
                            ->label('TikTok')
                            ->helperText('Barra flotante, menú hamburguesa y footer. Si está vacío, no se muestra.')
                            ->url()
                            ->maxLength(500),
                        Forms\Components\TextInput::make('youtube_url')
                            ->label('YouTube')
                            ->helperText('Barra flotante, menú hamburguesa y footer. Si está vacío, no se muestra.')
                            ->url()
                            ->maxLength(500),
                    ])->columns(2),
                Forms\Components\Section::make('Correo SMTP emisor')
                    ->description('Cuenta de marketing que envía los correos del sitio (libro de reclamaciones, formularios, etc.). En local el modo simulado no sale a internet.')
                    ->schema([
                        Forms\Components\Select::make('mail_mailer')
                            ->label('Modo de envío')
                            ->options([
                                'log' => 'Simulado (local, no sale a internet)',
                                'smtp' => 'SMTP real (marketing@)',
                            ])
                            ->required()
                            ->live()
                            ->helperText('En local deja Simulado. SMTP real usa mail.refugiogastronomico.pe.')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('mail_from_name')
                            ->label('Nombre del remitente')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('mail_from_address')
                            ->label('Correo emisor')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('mail_host')
                            ->label('Host SMTP')
                            ->maxLength(255)
                            ->visible(fn (Get $get): bool => $get('mail_mailer') === 'smtp'),
                        Forms\Components\TextInput::make('mail_port')
                            ->label('Puerto')
                            ->numeric()
                            ->placeholder('587')
                            ->visible(fn (Get $get): bool => $get('mail_mailer') === 'smtp'),
                        Forms\Components\TextInput::make('mail_username')
                            ->label('Usuario SMTP')
                            ->maxLength(255)
                            ->visible(fn (Get $get): bool => $get('mail_mailer') === 'smtp'),
                        Forms\Components\TextInput::make('mail_password')
                            ->label('Contraseña SMTP')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText('Déjala vacía para no cambiar la contraseña guardada.')
                            ->visible(fn (Get $get): bool => $get('mail_mailer') === 'smtp'),
                        Forms\Components\Select::make('mail_encryption')
                            ->label('Cifrado')
                            ->options([
                                'tls' => 'TLS (puerto 587)',
                                'ssl' => 'SSL (puerto 465)',
                                'none' => 'Ninguno',
                            ])
                            ->visible(fn (Get $get): bool => $get('mail_mailer') === 'smtp'),
                    ])->columns(2),
                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('Meta título')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('seo_description')
                            ->label('Meta descripción')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Medición Google')
                    ->description('IDs públicos de GTM y GA4. Se inyectan en el HTML; no son secretos. Si un campo queda vacío, se usa el valor de .env.')
                    ->schema([
                        Forms\Components\TextInput::make('google_tag_manager_id')
                            ->label('Google Tag Manager')
                            ->placeholder('GTM-XXXXXXX')
                            ->maxLength(32)
                            ->nullable()
                            ->regex('/^GTM-[A-Za-z0-9]+$/')
                            ->validationMessages([
                                'regex' => 'Usa un ID GTM válido, por ejemplo GTM-M8CTGV79.',
                            ]),
                        Forms\Components\TextInput::make('google_analytics_id')
                            ->label('Google Analytics 4')
                            ->placeholder('G-XXXXXXXXXX')
                            ->maxLength(32)
                            ->nullable()
                            ->regex('/^G-[A-Za-z0-9]+$/')
                            ->validationMessages([
                                'regex' => 'Usa un ID GA4 válido, por ejemplo G-4FCNED6QVR.',
                            ]),
                    ])->columns(2),
                Forms\Components\Section::make('Secciones del sitio')
                    ->description('Controla qué bloques aparecen en el frontend público.')
                    ->schema([
                        Forms\Components\Toggle::make('show_blog_section')
                            ->label('Habilitar rutas /blog')
                            ->helperText('El blog ya no aparece en home. Este toggle controla si /blog responde o devuelve 404. El enlace del footer sigue visible.')
                            ->default(true)
                            ->inline(false),
                    ]),
            ])
            ->statePath('data')
            ->model($this->record);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (blank($data['mail_password'] ?? null)) {
            unset($data['mail_password']);
        }

        if (($data['mail_mailer'] ?? null) !== 'smtp') {
            unset(
                $data['mail_host'],
                $data['mail_port'],
                $data['mail_username'],
                $data['mail_password'],
                $data['mail_encryption'],
            );
        }

        $this->record->fill($data)->save();
        $this->form->model($this->record)->saveRelationships();
        $this->record->refresh()->applyMailConfig();

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }
}
