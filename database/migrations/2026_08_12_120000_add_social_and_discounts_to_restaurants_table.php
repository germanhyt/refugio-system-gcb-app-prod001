<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('instagram_url', 500)->nullable()->after('whatsapp_url');
            $table->string('facebook_url', 500)->nullable()->after('instagram_url');
            $table->string('tiktok_url', 500)->nullable()->after('facebook_url');
            $table->json('corporate_discounts')->nullable()->after('delivery_peya_url');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'instagram_url',
                'facebook_url',
                'tiktok_url',
                'corporate_discounts',
            ]);
        });
    }
};
