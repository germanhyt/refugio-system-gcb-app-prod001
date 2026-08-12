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
                'email' => 'hola@refugiogastronomico.pe',
                'about_content' => 'TODO LO QUE TE PROVOCA, EN UN SOLO LUGAR.\n\nRefugio Gastronómico es el punto de encuentro donde la mejor gastronomía, el entretenimiento y los buenos momentos se unen en un solo espacio. Con más de 20 propuestas gastronómicas, música en vivo, eventos y experiencias para toda la familia, aquí siempre encontrarás un motivo para volver.',
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
}
