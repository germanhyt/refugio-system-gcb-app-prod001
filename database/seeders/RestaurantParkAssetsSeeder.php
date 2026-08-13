<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\HasMedia;

class RestaurantParkAssetsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Restaurant::query()->get() as $restaurant) {
            $restaurant->clearMediaCollection('menu_pdf');
            $this->attachIfPresent($restaurant, 'location_image', config('restaurant-assets.park_maps.'.$restaurant->slug));
            $this->attachIfPresent($restaurant, 'exclusive_discount_image', config('restaurant-assets.exclusive_discounts.'.$restaurant->slug));
            $this->attachIfPresent($restaurant, 'logo', config('restaurant-assets.logos.'.$restaurant->slug));
            $this->attachIfPresent($restaurant, 'featured_image', config('restaurant-assets.dishes.'.$restaurant->slug));
        }

        $settings = SiteSetting::current();

        foreach (config('restaurant-assets.page_banners', []) as $page => $relativePath) {
            $this->attachIfPresent($settings, 'hero_'.$page, $relativePath);
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
