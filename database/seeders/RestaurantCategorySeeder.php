<?php

namespace Database\Seeders;

use App\Models\RestaurantCategory;
use Illuminate\Database\Seeder;

class RestaurantCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Bar', 'slug' => 'bar', 'sort_order' => 1],
            ['name' => 'Cafés y postres', 'slug' => 'cafes-y-postres', 'sort_order' => 2],
            ['name' => 'Internacional', 'slug' => 'internacional', 'sort_order' => 3],
            ['name' => 'Peruana', 'slug' => 'peruana', 'sort_order' => 4],
            ['name' => 'Rápida', 'slug' => 'rapida', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            RestaurantCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
