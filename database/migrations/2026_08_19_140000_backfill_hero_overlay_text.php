<?php

use App\Models\HeroSlide;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        HeroSlide::query()
            ->where(function ($query) {
                $query->whereNull('title')->orWhere('title', '');
            })
            ->update(['title' => "¡DE TODO,\nPARA TODOS!"]);
    }

    public function down(): void
    {
        HeroSlide::query()
            ->where('title', "¡DE TODO,\nPARA TODOS!")
            ->update(['title' => '']);
    }
};
