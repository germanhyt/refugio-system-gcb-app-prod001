<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->string('website_url', 500)->nullable()->after('tiktok_url');
            $table->string('reservation_phone', 30)->nullable()->after('website_url');
            $table->string('corporate_discount_mode', 20)->default('none')->after('corporate_discounts');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'website_url',
                'reservation_phone',
                'corporate_discount_mode',
            ]);
        });
    }
};
