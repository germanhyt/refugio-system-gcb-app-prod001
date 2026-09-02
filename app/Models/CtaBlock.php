<?php

namespace App\Models;

use App\Support\SafePublicUrl;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CtaBlock extends Model
{
    protected $fillable = [
        'type',
        'title',
        'highlighted_word',
        'description',
        'link_url',
        'link_text',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function safeLinkUrl(): string
    {
        return SafePublicUrl::href($this->link_url, url('/#contacto'));
    }
}
