<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        HeroSlide::query()->update(['is_active' => false]);

        $slide = HeroSlide::query()->updateOrCreate(
            ['sort_order' => 0],
            [
                'title' => '',
                'subtitle' => null,
                'description' => null,
                'media_type' => 'video',
                'cta_text' => null,
                'cta_url' => null,
                'is_active' => true,
            ],
        );

        $slide->clearMediaCollection('background_video');
        $slide->clearMediaCollection('background_image');

        $videoPath = public_path('videos/video-principal.mp4');

        if (File::exists($videoPath)) {
            $slide->addMedia($videoPath)
                ->preservingOriginal()
                ->toMediaCollection('background_video');
        }

        $poster = public_path('images/refugio/fondohome.jpg');

        if (File::exists($poster)) {
            $slide->addMedia($poster)
                ->preservingOriginal()
                ->toMediaCollection('background_image');
        }
    }
}
