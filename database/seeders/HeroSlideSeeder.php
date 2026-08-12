<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        HeroSlide::query()
            ->whereRaw('lower(title) = ?', ['test'])
            ->delete();

        HeroSlide::query()
            ->orderByDesc('sort_order')
            ->get()
            ->each(fn (HeroSlide $slide) => $slide->update([
                'sort_order' => $slide->sort_order + 10,
            ]));

        $poster = public_path('images/refugio/fondohome.jpg');

        foreach ([
            ['sort_order' => 0, 'video' => 'videos/video1.mp4'],
            ['sort_order' => 1, 'video' => 'videos/video2.mp4'],
        ] as $item) {
            $slide = HeroSlide::query()->updateOrCreate(
                ['sort_order' => $item['sort_order']],
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

            $videoPath = public_path($item['video']);
            if (File::exists($videoPath) && ! $slide->getFirstMedia('background_video')) {
                $slide->addMedia($videoPath)
                    ->preservingOriginal()
                    ->toMediaCollection('background_video');
            }

            if (File::exists($poster) && ! $slide->getFirstMedia('background_image')) {
                $slide->addMedia($poster)
                    ->preservingOriginal()
                    ->toMediaCollection('background_image');
            }
        }

        HeroSlide::query()
            ->where('sort_order', '>=', 10)
            ->orderBy('sort_order')
            ->get()
            ->each(function (HeroSlide $slide, int $index) {
                $slide->update(['sort_order' => $index + 2]);
            });
    }
}
