<?php

namespace Database\Seeders;

use App\Models\EventOffer;
use App\Models\HeroSlide;
use App\Models\Restaurant;
use App\Models\VisitInfo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\HasMedia;

class LocalMediaImportSeeder extends Seeder
{
    public function run(): void
    {
        $this->importHeroVideo();
        $this->importAboutGallery();
        $this->importRestaurantAssets();
        $this->importEventOffers();
    }

    private function importHeroVideo(): void
    {
        HeroSlide::query()->update(['is_active' => false]);

        $slide = HeroSlide::query()->updateOrCreate(
            ['sort_order' => 0],
            [
                'title' => "¡DE TODO,\nPARA TODOS!",
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

        $this->attachIfPresent($slide, 'background_video', config('local-media-import.hero_video'));
    }

    private function importAboutGallery(): void
    {
        $visit = VisitInfo::current();
        $visit->clearMediaCollection('about_gallery');

        foreach (config('local-media-import.about_gallery', []) as $relativePath => $alt) {
            $path = public_path($relativePath);

            if (! File::exists($path)) {
                continue;
            }

            $visit
                ->addMedia($path)
                ->preservingOriginal()
                ->withCustomProperties(['alt' => $alt])
                ->toMediaCollection('about_gallery');
        }
    }

    private function importRestaurantAssets(): void
    {
        foreach (Restaurant::query()->get() as $restaurant) {
            $slug = $restaurant->slug;

            $this->attachIfPresent($restaurant, 'logo', config('local-media-import.logos.'.$slug));
            $this->attachIfPresent($restaurant, 'banner_image', config('local-media-import.banners.'.$slug));
            $this->attachIfPresent($restaurant, 'facade_image', config('local-media-import.frontis.'.$slug));
        }
    }

    private function importEventOffers(): void
    {
        foreach (config('local-media-import.event_offers', []) as $slug => $relativePath) {
            $offer = EventOffer::query()->where('slug', $slug)->first();

            if (! $offer) {
                continue;
            }

            $this->attachIfPresent($offer, 'cover', $relativePath);
        }
    }

    private function attachIfPresent(HasMedia $model, string $collection, mixed $relativePath): void
    {
        if (! is_string($relativePath) || $relativePath === '') {
            return;
        }

        $path = public_path($relativePath);

        if (! File::exists($path)) {
            return;
        }

        $model->clearMediaCollection($collection);
        $model
            ->addMedia($path)
            ->preservingOriginal()
            ->toMediaCollection($collection);
    }
}
