<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('whatsapp_url', 500)->nullable();
            $table->string('google_maps_url', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });

        Schema::create('restaurant_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_category_id')->constrained()->cascadeOnDelete();
            $table->unique(['restaurant_id', 'restaurant_category_id'], 'restaurant_category_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_category');
        Schema::dropIfExists('restaurants');
    }
};
