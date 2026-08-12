<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('hero_title_about')->nullable()->after('show_blog_section');
            $table->string('hero_title_restaurants')->nullable()->after('hero_title_about');
            $table->string('hero_title_events')->nullable()->after('hero_title_restaurants');
            $table->string('hero_title_services')->nullable()->after('hero_title_events');
        });

        DB::table('site_settings')->where('id', 1)->update([
            'hero_title_about' => "¿Quiénes\nSomos?",
            'hero_title_restaurants' => '¿Qué te provoca hoy?',
            'hero_title_events' => '¡Somos el refugio de tu diversión!',
            'hero_title_services' => 'Nuestros servicios',
        ]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title_about',
                'hero_title_restaurants',
                'hero_title_events',
                'hero_title_services',
            ]);
        });
    }
};
