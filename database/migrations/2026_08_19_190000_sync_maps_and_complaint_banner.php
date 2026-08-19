<?php

use App\Models\SiteSetting;
use Database\Seeders\RestaurantMapSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('hero_title_complaints')->nullable()->after('hero_title_services');
        });

        SiteSetting::query()->whereKey(1)->update([
            'hero_title_complaints' => 'Libro de reclamaciones',
        ]);

        (new RestaurantMapSeeder)->run();
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('hero_title_complaints');
        });
    }
};
