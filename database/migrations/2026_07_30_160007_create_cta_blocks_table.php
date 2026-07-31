<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cta_blocks', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['evento', 'renta', 'contacto']);
            $table->string('title');
            $table->string('highlighted_word', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('link_url', 500)->nullable();
            $table->string('link_text', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cta_blocks');
    }
};
