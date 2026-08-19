<?php

use App\Models\SiteSetting;
use App\Support\PublicMedia;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $settings = SiteSetting::query()->find(1);
        if (! $settings) {
            return;
        }

        PublicMedia::syncPublic(
            $settings,
            'hero_about',
            'images/refugio/nosotros-banner.png',
        );
    }

    public function down(): void
    {
        SiteSetting::query()->find(1)?->clearMediaCollection('hero_about');
    }
};
