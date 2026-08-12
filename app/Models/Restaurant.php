<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Restaurant extends Model implements HasMedia
{
    use HasSlug;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'whatsapp_url',
        'delivery_rappi_enabled',
        'delivery_rappi_url',
        'delivery_peya_enabled',
        'delivery_peya_url',
        'google_maps_url',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'delivery_rappi_enabled' => 'boolean',
            'delivery_peya_enabled' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('featured_image')->singleFile();
        $this->addMediaCollection('location_image')->singleFile();
        $this->addMediaCollection('menu_pdf')->singleFile()->acceptsMimeTypes(['application/pdf']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->performOnCollections('logo', 'featured_image', 'location_image')
            ->nonQueued();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            RestaurantCategory::class,
            'restaurant_category',
            'restaurant_id',
            'restaurant_category_id'
        );
    }

    public function homeFeature(): HasOne
    {
        return $this->hasOne(HomeRestaurantFeature::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function hasDeliveryOptions(): bool
    {
        return ($this->delivery_rappi_enabled && filled($this->delivery_rappi_url))
            || ($this->delivery_peya_enabled && filled($this->delivery_peya_url));
    }

    public function showsDeliveryLogos(): bool
    {
        return $this->delivery_rappi_enabled || $this->delivery_peya_enabled;
    }
}
