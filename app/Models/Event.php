<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Event extends Model implements HasMedia
{
    use HasSlug;
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'event_date',
        'event_time',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->format('webp')
            ->performOnCollections('featured_image')
            ->nonQueued();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getDayAbbreviationAttribute(): string
    {
        $map = [
            'Mon' => 'Lun',
            'Tue' => 'Mar',
            'Wed' => 'Mié',
            'Thu' => 'Jue',
            'Fri' => 'Vie',
            'Sat' => 'Sáb',
            'Sun' => 'Dom',
        ];

        $abbr = Carbon::parse($this->event_date)->format('D');

        return $map[$abbr] ?? $abbr;
    }

    public function getDayNumberAttribute(): string
    {
        return Carbon::parse($this->event_date)->format('d');
    }
}
