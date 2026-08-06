<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaint_book_entries', function (Blueprint $table) {
            $table->id();
            $table->string('document_type', 20);
            $table->string('document_number', 40);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('department', 80);
            $table->string('address');
            $table->string('phone', 40);
            $table->string('email');
            $table->string('parent_name')->nullable();
            $table->string('claimed_amount', 80)->nullable();
            $table->text('product_description');
            $table->string('claim_type', 20);
            $table->text('claim_detail');
            $table->text('consumer_request');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_book_entries');
    }
};
