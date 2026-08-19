<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HeroSlide extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const DEFAULT_OVERLAY = "¡DE TODO,\nPARA TODOS!";

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'media_type',
        'cta_text',
        'cta_url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('background_image')->singleFile();
        $this->addMediaCollection('background_video')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if (! function_exists('imagewebp')) {
            return;
        }

        $this->addMediaConversion('webp')
            ->format('webp')
            ->performOnCollections('background_image')
            ->nonQueued();
    }

    public function overlayText(): string
    {
        $text = trim((string) $this->title);

        return $text !== '' ? $text : self::DEFAULT_OVERLAY;
    }

    public function overlayHtml(): string
    {
        return nl2br(e($this->overlayText()), false);
    }

    public function isVideo(): bool
    {
        return ($this->media_type ?? 'image') === 'video';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
