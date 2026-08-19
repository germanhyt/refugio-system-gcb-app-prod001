<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class VisitInfo extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const DEFAULT_HOLA_EYEBROW = '¡Hola! Somos Refugio Gastronómico.';

    public const DEFAULT_HOLA_HEADLINE = 'TODO LO QUE TE PROVOCA, EN UN SOLO LUGAR.';

    public const DEFAULT_HOLA_BODY = 'Refugio Gastronómico es el punto de encuentro donde la mejor gastronomía, el entretenimiento y los buenos momentos se unen en un solo espacio. Con más de 20 propuestas gastronómicas, música en vivo, eventos y experiencias para toda la familia, aquí siempre encontrarás un motivo para volver.';

    public const DEFAULT_MAP_EMBED_URL = 'https://maps.google.com/maps?q=-12.0842658,-76.9734978&z=16&hl=es&t=m&output=embed';

    protected $table = 'visit_info';

    protected $fillable = [
        'address',
        'schedule',
        'phone_reservations',
        'phone_events',
        'email',
        'map_embed_url',
        'pedestrian_access',
        'vehicle_access',
        'amenities',
        'about_content',
        'about_eyebrow',
    ];

    protected function casts(): array
    {
        return [
            'schedule' => 'array',
            'amenities' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'address' => 'Av. Javier Prado Este 4492',
                'schedule' => [
                    ['days' => 'Domingo a Miércoles', 'hours' => 'Hasta las 10 p.m.'],
                    ['days' => 'Jueves', 'hours' => 'Hasta la 1:00 p.m.'],
                    ['days' => 'Viernes y sábado', 'hours' => 'Hasta las 3:00 p.m.'],
                    ['days' => 'Música en vivo', 'hours' => 'Jueves a viernes, desde las 8:00 p.m.'],
                ],
                'phone_reservations' => '991318720',
                'phone_events' => '994848723',
                'email' => 'leilah@gcb.pe',
                'map_embed_url' => self::DEFAULT_MAP_EMBED_URL,
                'about_content' => self::composeAboutContent(self::DEFAULT_HOLA_HEADLINE, self::DEFAULT_HOLA_BODY),
                'about_eyebrow' => self::DEFAULT_HOLA_EYEBROW,
                'amenities' => [
                    'Pet Friendly',
                    '3 horas de estacionamiento gratis con S/50 de consumo',
                ],
            ]
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('about_gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if (! function_exists('imagewebp')) {
            return;
        }

        $this->addMediaConversion('webp')
            ->format('webp')
            ->performOnCollections('about_gallery')
            ->nonQueued();
    }

    public function aboutGallerySlides(): Collection
    {
        return $this->getMedia('about_gallery')->map(fn (Media $media) => [
            'url' => $media->hasGeneratedConversion('webp') ? $media->getUrl('webp') : $media->getUrl(),
            'alt' => $media->getCustomProperty('alt') ?: 'Refugio Gastronómico',
        ]);
    }

    /**
     * @return array{headline: string, body: string}
     */
    public function aboutEyebrow(): string
    {
        $text = trim((string) $this->about_eyebrow);

        return $text !== '' ? $text : self::DEFAULT_HOLA_EYEBROW;
    }

    public function holaCopy(): array
    {
        $plain = self::plainAboutText($this->about_content);

        if ($plain === '') {
            return [
                'headline' => self::DEFAULT_HOLA_HEADLINE,
                'body' => self::DEFAULT_HOLA_BODY,
            ];
        }

        $parts = preg_split("/\n\s*\n/", $plain, 2) ?: [''];
        $first = trim((string) ($parts[0] ?? ''));
        $second = trim((string) ($parts[1] ?? ''));

        if ($second !== '') {
            return [
                'headline' => $first,
                'body' => $second,
            ];
        }

        $headlineLength = mb_strlen(self::DEFAULT_HOLA_HEADLINE);
        if (mb_stripos($first, self::DEFAULT_HOLA_HEADLINE) === 0) {
            $after = trim(mb_substr($first, $headlineLength));
            $after = ltrim($after, " \t.");

            return [
                'headline' => self::DEFAULT_HOLA_HEADLINE,
                'body' => $after !== '' ? $after : self::DEFAULT_HOLA_BODY,
            ];
        }

        if (preg_match('/^(.{10,90}[.!?])\s+(.+)$/su', $first, $match) === 1) {
            return [
                'headline' => trim($match[1]),
                'body' => trim($match[2]),
            ];
        }

        return [
            'headline' => $first,
            'body' => self::DEFAULT_HOLA_BODY,
        ];
    }

    public static function composeAboutContent(string $headline, string $body): string
    {
        return trim($headline)."\n\n".trim($body);
    }

    public static function plainAboutText(?string $raw): string
    {
        $raw = (string) $raw;
        if ($raw === '') {
            return '';
        }

        $normalized = preg_replace('/<\/(p|div|h[1-6]|li|blockquote)>/i', "\n\n", $raw) ?? $raw;
        $normalized = preg_replace('/<br\s*\/?>/i', "\n", $normalized) ?? $normalized;
        $normalized = html_entity_decode(strip_tags($normalized), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $normalized = str_replace(["\r\n", "\r", '\n'], "\n", $normalized);
        $normalized = preg_replace("/[ \t]+/", ' ', $normalized) ?? $normalized;
        $normalized = preg_replace("/\n{3,}/", "\n\n", $normalized) ?? $normalized;

        return trim($normalized);
    }

    public static function normalizeMapEmbedUrl(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return self::DEFAULT_MAP_EMBED_URL;
        }

        if (preg_match('/\bsrc=["\']([^"\']+)["\']/i', $value, $match) === 1) {
            $value = html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $value = html_entity_decode(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if (
            str_contains($value, 'google.com/maps')
            && ! str_contains($value, 'output=embed')
            && ! str_contains($value, '/maps/embed')
        ) {
            $value .= (str_contains($value, '?') ? '&' : '?').'output=embed';
        }

        return $value;
    }

    public function mapEmbedUrl(): string
    {
        return self::normalizeMapEmbedUrl($this->map_embed_url);
    }
}
