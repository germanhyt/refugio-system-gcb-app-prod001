<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->text('short_description')->nullable()->after('description');
            $table->boolean('delivery_rappi_enabled')->default(false)->after('whatsapp_url');
            $table->string('delivery_rappi_url', 500)->nullable()->after('delivery_rappi_enabled');
            $table->boolean('delivery_peya_enabled')->default(false)->after('delivery_rappi_url');
            $table->string('delivery_peya_url', 500)->nullable()->after('delivery_peya_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'short_description',
                'delivery_rappi_enabled',
                'delivery_rappi_url',
                'delivery_peya_enabled',
                'delivery_peya_url',
            ]);
        });
    }
};
