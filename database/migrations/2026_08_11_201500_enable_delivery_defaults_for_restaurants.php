<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('restaurants')->update([
            'delivery_rappi_enabled' => true,
            'delivery_peya_enabled' => true,
        ]);
    }

    public function down(): void
    {
        DB::table('restaurants')->update([
            'delivery_rappi_enabled' => false,
            'delivery_peya_enabled' => false,
        ]);
    }
};
