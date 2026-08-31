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
            $table->string('google_tag_manager_id', 32)->nullable()->after('seo_description');
            $table->string('google_analytics_id', 32)->nullable()->after('google_tag_manager_id');
        });

        DB::table('site_settings')->where('id', 1)->update([
            'google_tag_manager_id' => 'GTM-M8CTGV79',
            'google_analytics_id' => 'G-4FCNED6QVR',
        ]);
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['google_tag_manager_id', 'google_analytics_id']);
        });
    }
};
