<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaticPage extends Model
{
    public const SLUG_FAQ = 'faq';

    public const SLUG_PET_FRIENDLY = 'pet-friendly';

    public const SLUG_PARKING = 'parking';

    public const SLUG_ULIMA = 'ulima';

    protected $fillable = [
        'slug',
        'title',
        'intro',
        'blocks',
    ];

    protected function casts(): array
    {
        return [
            'blocks' => 'array',
        ];
    }

    public static function slugLabels(): array
    {
        return [
            self::SLUG_FAQ => 'Preguntas Frecuentes',
            self::SLUG_PET_FRIENDLY => 'Reglamento de Pet Friendly',
            self::SLUG_PARKING => 'Política de estacionamiento',
            self::SLUG_ULIMA => 'Descuentos U. Lima',
        ];
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::query()->where('slug', $slug)->first();
    }

    public function isDocumentRedirect(): bool
    {
        return $this->slug === self::SLUG_ULIMA;
    }

    public function toPageArray(): array
    {
        $defaults = config("static-pages.{$this->slug}", []);

        return [
            'title' => $this->title,
            'intro' => $this->intro,
            'hero_image' => $defaults['hero_image'] ?? 'images/refugio/bg-pagajaras-negro-1.png',
            'blocks' => $this->blocks ?? [],
        ];
    }

    public function routeUrl(): ?string
    {
        return match ($this->slug) {
            self::SLUG_FAQ => route('static.faq'),
            self::SLUG_PET_FRIENDLY => route('static.pet-friendly'),
            self::SLUG_PARKING => route('static.parking'),
            self::SLUG_ULIMA => route('static.ulima'),
            default => null,
        };
    }
}
