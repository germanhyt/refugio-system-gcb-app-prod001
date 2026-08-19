<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SiteSetting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'site_name',
        'slogan',
        'whatsapp_url',
        'instagram_url',
        'facebook_url',
        'tiktok_url',
        'youtube_url',
        'seo_title',
        'seo_description',
        'show_blog_section',
        'show_fixed_social',
        'hero_title_about',
        'hero_title_restaurants',
        'hero_title_events',
        'hero_title_services',
        'hero_title_complaints',
        'complaint_book_recipients',
        'mail_from_name',
        'mail_from_address',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_mailer',
    ];

    protected function casts(): array
    {
        return [
            'show_blog_section' => 'boolean',
            'show_fixed_social' => 'boolean',
            'complaint_book_recipients' => 'array',
            'mail_port' => 'integer',
            'mail_password' => 'encrypted',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('favicon')->singleFile();
        $this->addMediaCollection('og_image')->singleFile();
        $this->addMediaCollection('ulima_discounts_pdf')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
        $this->addMediaCollection('hero_about')->singleFile();
        $this->addMediaCollection('hero_restaurants')->singleFile();
        $this->addMediaCollection('hero_services')->singleFile();
        $this->addMediaCollection('hero_events')->singleFile();
        $this->addMediaCollection('hero_complaints')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if (! function_exists('imagewebp')) {
            return;
        }

        $this->addMediaConversion('webp')
            ->format('webp')
            ->performOnCollections('logo', 'og_image', 'hero_about', 'hero_restaurants', 'hero_services', 'hero_events', 'hero_complaints')
            ->nonQueued();
    }

    public function pageHeroBannerUrl(string $page): ?string
    {
        $url = $this->getFirstMediaUrl('hero_'.$page);

        return filled($url) ? $url : null;
    }

    /**
     * @return array<string, string>
     */
    public function heroMediaFormState(string $collection): array
    {
        $this->loadMissing('media');
        $media = $this->getFirstMedia($collection);

        if (! $media) {
            return [];
        }

        $uuid = (string) $media->getAttributeValue('uuid');

        return [$uuid => $uuid];
    }

    /**
     * @return list<string>
     */
    public function complaintBookRecipients(): array
    {
        $stored = $this->complaint_book_recipients;

        if (is_array($stored) && $stored !== []) {
            return array_values(array_filter(array_map(
                static fn ($email): string => trim((string) $email),
                $stored
            )));
        }

        return config('refugio.complaint_book_recipients', []);
    }

    public function resolvedMailer(): string
    {
        if (in_array($this->mail_mailer, ['log', 'smtp'], true)) {
            return $this->mail_mailer;
        }

        if (app()->environment('local', 'testing')) {
            return 'log';
        }

        return filled($this->mail_host) ? 'smtp' : (string) config('mail.default', 'log');
    }

    public function usesSimulatedMail(): bool
    {
        return $this->resolvedMailer() === 'log';
    }

    public function applyMailConfig(): void
    {
        if (filled($this->mail_from_address)) {
            config(['mail.from.address' => $this->mail_from_address]);
        }

        if (filled($this->mail_from_name)) {
            config(['mail.from.name' => $this->mail_from_name]);
        }

        if ($this->usesSimulatedMail()) {
            config(['mail.default' => 'log']);

            return;
        }

        if (blank($this->mail_host)) {
            return;
        }

        $encryption = $this->mail_encryption ?: 'tls';
        $scheme = $encryption === 'ssl' ? 'smtps' : null;

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $this->mail_host,
            'mail.mailers.smtp.port' => (int) ($this->mail_port ?: ($encryption === 'ssl' ? 465 : 587)),
            'mail.mailers.smtp.username' => $this->mail_username,
            'mail.mailers.smtp.password' => $this->mail_password,
            'mail.mailers.smtp.scheme' => $scheme,
            'mail.mailers.smtp.timeout' => 8,
        ]);
    }

    public function ulimaDiscountsPdfUrl(): ?string
    {
        $url = $this->getFirstMediaUrl('ulima_discounts_pdf');

        if (filled($url)) {
            return $url;
        }

        foreach ([
            'docs/ULIMA-DESCUENTOS.pdf',
            'images/ULIMA-DESCUENTOS.pdf',
            'images/nuevo/ULIMA-DESCUENTOS.pdf',
        ] as $relative) {
            if (is_file(public_path($relative))) {
                return asset($relative);
            }
        }

        return null;
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'site_name' => 'Refugio Gastronómico',
                'slogan' => 'Juntos todo sabe mejor',
                'whatsapp_url' => 'https://wa.link/ltbwxk',
                'instagram_url' => 'https://www.instagram.com/refugiogastronomico.pe/',
                'facebook_url' => 'https://www.facebook.com/RefugioParqueGastronomico',
                'tiktok_url' => 'https://www.tiktok.com/@refugiogastronomico.pe',
                'youtube_url' => null,
                'seo_title' => 'Refugio Gastronómico | Juntos todo sabe mejor',
                'seo_description' => '¡Descubre Refugio! Disfruta de una gran variedad de opciones gastronómicas, bebidas, música en vivo, talleres y actividades en Surco.',
                'show_blog_section' => true,
                'show_fixed_social' => true,
                'hero_title_about' => "¿Quiénes\nSomos?",
                'hero_title_restaurants' => '¿Qué te provoca hoy?',
                'hero_title_events' => '¡Somos el refugio de tu diversión!',
                'hero_title_services' => 'Nuestros servicios',
                'hero_title_complaints' => 'Libro de reclamaciones',
                'complaint_book_recipients' => [
                    'leilah@gcb.pe',
                    'mario@refugiogastronomico.pe',
                    'nataly@gcb.pe',
                ],
            ]
        );
    }
}
