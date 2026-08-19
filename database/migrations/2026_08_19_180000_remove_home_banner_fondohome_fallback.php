<?php

use App\Models\HeroSlide;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        foreach (HeroSlide::query()->where('media_type', 'video')->get() as $slide) {
            $poster = $slide->getFirstMedia('background_image');
            if (! $poster) {
                continue;
            }

            $name = strtolower((string) $poster->file_name);
            if (str_contains($name, 'fondohome')) {
                $poster->delete();
            }
        }
    }

    public function down(): void
    {
        //
    }
};
