<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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
        'instagram_url',
        'facebook_url',
        'tiktok_url',
        'delivery_rappi_enabled',
        'delivery_rappi_url',
        'delivery_peya_enabled',
        'delivery_peya_url',
        'corporate_discounts',
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
            'corporate_discounts' => 'array',
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

    public function parkPositionImageUrl(): ?string
    {
        $url = $this->getFirstMediaUrl('location_image');

        return filled($url) ? $url : null;
    }

    /**
     * @return list<array{key: string, label: string, url: string}>
     */
    public function socialLinks(): array
    {
        $links = [
            ['key' => 'instagram', 'label' => 'Instagram', 'url' => $this->instagram_url],
            ['key' => 'facebook', 'label' => 'Facebook', 'url' => $this->facebook_url],
            ['key' => 'tiktok', 'label' => 'TikTok', 'url' => $this->tiktok_url],
            ['key' => 'whatsapp', 'label' => 'WhatsApp', 'url' => $this->whatsapp_url],
        ];

        return array_values(array_filter(
            $links,
            fn (array $link): bool => filled($link['url'])
        ));
    }

    public function hasSocialLinks(): bool
    {
        return $this->socialLinks() !== [];
    }

    public function hasDetailAside(): bool
    {
        return $this->showsDeliveryLogos() || filled($this->parkPositionImageUrl());
    }

    public function detailLeadText(): ?string
    {
        $short = $this->normalizedPlain((string) $this->short_description);

        if ($short === '') {
            return null;
        }

        $body = $this->normalizedPlain((string) $this->detailBodyHtml());

        if ($body === '') {
            return $this->short_description;
        }

        $shortKey = mb_strtolower($short);
        $bodyKey = mb_strtolower($body);

        if ($shortKey === $bodyKey || str_starts_with($bodyKey, $shortKey)) {
            return null;
        }

        return $this->short_description;
    }

    public function detailBodyHtml(): ?string
    {
        $html = trim((string) $this->description);

        if ($html === '') {
            return null;
        }

        if (preg_match_all('/<p\b[^>]*>.*?<\/p>/is', $html, $matches) && $matches[0] !== []) {
            $unique = [];
            $lastPlain = null;

            foreach ($matches[0] as $paragraph) {
                $plain = mb_strtolower($this->normalizedPlain($paragraph));

                if ($plain === '' || $plain === $lastPlain) {
                    continue;
                }

                $unique[] = $paragraph;
                $lastPlain = $plain;
            }

            if ($unique !== []) {
                $html = implode('', $unique);
            }
        }

        return $html !== '' ? $html : null;
    }

    public function visibleCorporateDiscounts(?Carbon $on = null): Collection
    {
        $today = ($on ?? now('America/Lima'))->copy()->startOfDay();

        return collect($this->corporate_discounts ?? [])
            ->map(function (mixed $item) use ($today): ?array {
                if (! is_array($item) || ! filled($item['title'] ?? null)) {
                    return null;
                }

                if (! ($item['is_active'] ?? true)) {
                    return null;
                }

                $startsAt = filled($item['starts_at'] ?? null)
                    ? Carbon::parse($item['starts_at'], 'America/Lima')->startOfDay()
                    : null;
                $endsAt = filled($item['ends_at'] ?? null)
                    ? Carbon::parse($item['ends_at'], 'America/Lima')->endOfDay()
                    : null;

                if ($endsAt && $today->gt($endsAt)) {
                    return null;
                }

                $item['status'] = ($startsAt && $today->lt($startsAt)) ? 'upcoming' : 'current';

                return $item;
            })
            ->filter()
            ->values();
    }

    public function activeCorporateDiscounts(?Carbon $on = null): Collection
    {
        return $this->visibleCorporateDiscounts($on)
            ->filter(fn (array $item): bool => ($item['status'] ?? 'current') === 'current')
            ->values();
    }

    private function normalizedPlain(string $value): string
    {
        $plain = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/u', ' ', $plain));
    }
}
