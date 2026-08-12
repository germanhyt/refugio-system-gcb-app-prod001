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
            'emergency' => 'Emergencia / botiquín',
        ];
    }
}
