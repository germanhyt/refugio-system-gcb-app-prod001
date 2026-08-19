<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactBlock extends Model
{
    protected $fillable = [
        'title',
        'body',
        'emails',
        'phones',
        'sort_order',
        'is_active',
        'redirects_enabled',
    ];

    protected function casts(): array
    {
        return [
            'emails' => 'array',
            'phones' => 'array',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'redirects_enabled' => 'boolean',
        ];
    }

    protected $attributes = [
        'redirects_enabled' => true,
    ];

    public function redirects(): bool
    {
        return $this->redirects_enabled !== false;
    }

    /**
     * @return list<array{label: string, href: ?string, external: bool}>
     */
    public function contactChannels(): array
    {
        $channels = [];

        foreach ($this->emails ?? [] as $email) {
            $email = trim((string) $email);
            if ($email === '') {
                continue;
            }

            $channels[] = [
                'label' => $email,
                'href' => $this->redirects() ? 'mailto:'.$email : null,
                'external' => false,
            ];
        }

        foreach ($this->phones ?? [] as $phone) {
            $phone = trim((string) $phone);
            if ($phone === '') {
                continue;
            }

            $href = $this->redirects() ? $this->phoneHref($phone) : null;

            $channels[] = [
                'label' => $phone,
                'href' => $href,
                'external' => is_string($href) && str_starts_with($href, 'https://'),
            ];
        }

        return $channels;
    }

    public function phoneHref(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?: '';

        if (strlen($digits) === 9) {
            return 'https://wa.me/51'.$digits;
        }

        if (str_starts_with($digits, '51') && strlen($digits) >= 11) {
            return 'https://wa.me/'.$digits;
        }

        return 'tel:'.preg_replace('/\s+/', '', $phone);
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
