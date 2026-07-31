<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_info', function (Blueprint $table) {
            $table->id();
            $table->string('address', 500);
            $table->json('schedule');
            $table->string('phone_reservations', 20)->nullable();
            $table->string('phone_events', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('map_embed_url')->nullable();
            $table->text('pedestrian_access')->nullable();
            $table->text('vehicle_access')->nullable();
            $table->json('amenities')->nullable();
            $table->text('about_content')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_info');
    }
};
