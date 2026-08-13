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
        'website_url',
        'reservation_phone',
        'delivery_rappi_enabled',
        'delivery_rappi_url',
        'delivery_peya_enabled',
        'delivery_peya_url',
        'corporate_discounts',
        'corporate_discount_mode',
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
            'corporate_discount_mode' => 'string',
        ];
    }

    public const DISCOUNT_NONE = 'none';

    public const DISCOUNT_BADGE = 'badge';

    public const DISCOUNT_DETAILS = 'details';

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
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
        $this->addMediaCollection('exclusive_discount_image')->singleFile();
        $this->addMediaCollection('menu_pdf')->singleFile()->acceptsMimeTypes(['application/pdf']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->performOnCollections('featured_image', 'location_image')
            ->nonQueued();
    }

    public function logoUrl(): ?string
    {
        $url = $this->getFirstMediaUrl('logo');

        if (filled($url)) {
            return $url;
        }

        return $this->publicAssetUrl(config('restaurant-assets.logos.'.$this->slug));
    }

    public function featuredImageUrl(): ?string
    {
        $url = $this->getFirstMediaUrl('featured_image');

        if (filled($url)) {
            return $url;
        }

        return $this->publicAssetUrl(config('restaurant-assets.dishes.'.$this->slug));
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

    public function scopeOrderedByCategory(Builder $query): Builder
    {
        return $query->orderBy(
            RestaurantCategory::query()
                ->selectRaw('coalesce(min(restaurant_categories.sort_order), 99)')
                ->join('restaurant_category', 'restaurant_categories.id', '=', 'restaurant_category.restaurant_category_id')
                ->whereColumn('restaurant_category.restaurant_id', 'restaurants.id')
        )->orderBy('sort_order')->orderBy('name');
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

        if (filled($url)) {
            return $url;
        }

        return $this->publicAssetUrl(config('restaurant-assets.park_maps.'.$this->slug));
    }

    public function exclusiveDiscountImageUrl(): ?string
    {
        $url = $this->getFirstMediaUrl('exclusive_discount_image');

        if (filled($url)) {
            return $url;
        }

        return $this->publicAssetUrl(config('restaurant-assets.exclusive_discounts.'.$this->slug));
    }

    public function showsExclusiveDiscount(): bool
    {
        return $this->showsCorporateDiscountBadge() || filled($this->exclusiveDiscountImageUrl());
    }

    /**
     * @return list<array{key: string, label: string, url: string}>
     */
    public function socialLinks(): array
    {
        $links = [
            ['key' => 'website', 'label' => 'Página web', 'url' => $this->website_url],
            ['key' => 'instagram', 'label' => 'Instagram', 'url' => $this->instagram_url],
            ['key' => 'facebook', 'label' => 'Facebook', 'url' => $this->facebook_url],
            ['key' => 'tiktok', 'label' => 'TikTok', 'url' => $this->tiktok_url],
        ];

        return array_values(array_filter(
            $links,
            function (array $link): bool {
                if (! filled($link['url'])) {
                    return false;
                }

                if ($link['key'] === 'facebook' && ! str_contains((string) $link['url'], 'facebook.com')) {
                    return false;
                }

                return true;
            }
        ));
    }

    public function hasSocialLinks(): bool
    {
        return $this->socialLinks() !== [];
    }

    public function reservationWhatsappUrl(): ?string
    {
        if (filled($this->whatsapp_url)) {
            return $this->whatsapp_url;
        }

        $digits = preg_replace('/\D+/', '', (string) $this->reservation_phone);

        if (strlen((string) $digits) === 9) {
            return 'https://wa.me/51'.$digits;
        }

        return null;
    }

    public function hasReservationWhatsapp(): bool
    {
        return filled($this->reservationWhatsappUrl());
    }

    public function showsCorporateDiscountBadge(): bool
    {
        return $this->corporate_discount_mode === self::DISCOUNT_BADGE;
    }

    public function showsCorporateDiscountDetails(): bool
    {
        return $this->corporate_discount_mode === self::DISCOUNT_DETAILS
            && $this->visibleCorporateDiscounts()->isNotEmpty();
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

    private function publicAssetUrl(mixed $relativePath): ?string
    {
        if (! is_string($relativePath) || $relativePath === '') {
            return null;
        }

        if (! is_file(public_path($relativePath))) {
            return null;
        }

        return asset($relativePath);
    }

    private function normalizedPlain(string $value): string
    {
        $plain = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/u', ' ', $plain));
    }
}
