<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ServiceItem extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'icon_key',
        'description',
        'contact_phone',
        'whatsapp_message',
        'sort_order',
        'show_on_home',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'show_on_home' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->performOnCollections('icon')
            ->nonQueued();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeShowOnHome(Builder $query): Builder
    {
        return $query->where('show_on_home', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    public function descriptionWithWhatsappLinks(): string
    {
        $safe = e((string) $this->description);
        $phone = $this->normalizedContactPhone();

        if ($phone !== null) {
            $display = $this->displayContactPhone();
            $anchor = $this->whatsappAnchor($phone, $display);
            $pattern = $this->phoneSearchPattern($phone);

            if ($pattern !== null && preg_match($pattern, $safe)) {
                return (string) preg_replace($pattern, $anchor, $safe, 1);
            }

            return trim($safe.' '.$anchor);
        }

        return (string) preg_replace_callback(
            '/\b(\d{3}(?:\s\d{3}){2}|\d{9})\b/',
            function (array $match): string {
                $digits = preg_replace('/\D+/', '', $match[1]);
                $phoneDigits = str_starts_with((string) $digits, '51') ? $digits : '51'.$digits;

                return $this->whatsappAnchor((string) $phoneDigits, $match[1]);
            },
            $safe
        );
    }

    public function displayContactPhone(): string
    {
        $raw = trim((string) $this->contact_phone);

        if ($raw !== '') {
            return $raw;
        }

        return '';
    }

    public function normalizedContactPhone(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->contact_phone);

        if (! is_string($digits) || strlen($digits) < 9) {
            return null;
        }

        return str_starts_with($digits, '51') ? $digits : '51'.$digits;
    }

    private function phoneSearchPattern(string $phoneDigits): ?string
    {
        $national = str_starts_with($phoneDigits, '51') ? substr($phoneDigits, 2) : $phoneDigits;

        if (! preg_match('/^\d{9}$/', $national)) {
            return null;
        }

        $spaced = substr($national, 0, 3).'\s*'.substr($national, 3, 3).'\s*'.substr($national, 6, 3);

        return '/\b(?:51)?(?:\s*)?(?:'.$national.'|'.$spaced.')\b/';
    }

    private function whatsappAnchor(string $phoneDigits, string $label): string
    {
        $message = trim((string) $this->whatsapp_message);
        if ($message === '') {
            $message = 'Hola Refugio Gastronómico, quiero información sobre '.$this->title.'.';
        }

        return '<a class="rg-service-whatsapp" href="https://wa.me/'.$phoneDigits.'?text='.rawurlencode($message).'" target="_blank" rel="noopener noreferrer" data-rg-track="click_whatsapp" data-rg-label="'.e($this->title).'">'.$label.'</a>';
    }

    public static function iconKeyOptions(): array
    {
        return [
            'parking' => 'Estacionamiento',
            'restrooms' => 'Baños',
            'kids-restroom' => 'Baños para niños',
            'kids-zone' => 'Zona infantil',
            'delivery' => 'Delivery',
            'reservations' => 'Reservas / calendario',
            'pet' => 'Pet friendly',
            'whatsapp' => 'WhatsApp',
            'live-shows' => 'Shows en vivo / micrófono',
            'kids-shows' => 'Shows infantiles',
            'event-spaces' => 'Espacios para eventos',
            'lost-found' => 'Objetos perdidos',
            'ads' => 'Publicidad / megáfono',
            'zones' => 'Zonas / layout',
            'catering' => 'Catering',
            'nursing' => 'Lactancia',
            'emergency' => 'Tópico / primeros auxilios',
        ];
    }
}
