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
        'hero_title_about',
        'hero_title_restaurants',
        'hero_title_events',
        'hero_title_services',
    ];

    protected function casts(): array
    {
        return [
            'show_blog_section' => 'boolean',
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
        $this->addMediaCollection('hero_restaurants')->singleFile();
        $this->addMediaCollection('hero_services')->singleFile();
        $this->addMediaCollection('hero_events')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if (! function_exists('imagewebp')) {
            return;
        }

        $this->addMediaConversion('webp')
            ->format('webp')
            ->performOnCollections('logo', 'og_image', 'hero_restaurants', 'hero_services', 'hero_events')
            ->nonQueued();
    }

    public function pageHeroBannerUrl(string $page): string
    {
        $url = $this->getFirstMediaUrl('hero_'.$page);

        if (filled($url)) {
            return $url;
        }

        $relative = config('restaurant-assets.page_banners.'.$page);

        if (is_string($relative) && is_file(public_path($relative))) {
            return asset($relative);
        }

        return $page === 'events'
            ? asset('images/refugio/eventos-hero.jpg')
            : asset('images/refugio/restaurantes-hero.jpg');
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
                'hero_title_about' => "¿Quiénes\nSomos?",
                'hero_title_restaurants' => '¿Qué te provoca hoy?',
                'hero_title_events' => '¡Somos el refugio de tu diversión!',
                'hero_title_services' => 'Nuestros servicios',
            ]
        );
    }
}
