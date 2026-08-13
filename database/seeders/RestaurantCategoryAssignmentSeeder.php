<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\RestaurantCategory;
use Illuminate\Database\Seeder;

class RestaurantCategoryAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'refugio-bar' => ['bar'],
            'tortas-gaby' => ['cafes-y-postres'],
            'cremoladas-curich' => ['cafes-y-postres'],
            'sisa-coffee-wine' => ['cafes-y-postres'],
            'lili-blue' => ['internacional'],
            'ahumare' => ['internacional'],
            'barrio-wok' => ['internacional'],
            'cavenecia' => ['internacional'],
            'hanzo-express' => ['internacional'],
            'nashmys' => ['internacional'],
            'ramen-ya' => ['internacional'],
            'anticuchos-anticuching' => ['peruana'],
            'barrio-mancora' => ['peruana'],
            'caldos-doris' => ['peruana'],
            'don-melchor' => ['peruana'],
            'la-victoria' => ['peruana'],
            'madre-amazonica' => ['peruana'],
            'saltao-wok-food' => ['peruana'],
            'broaster-bros' => ['rapida'],
            'la-22-sangucheria' => ['rapida'],
            'limanesas' => ['rapida'],
            'mr-smash' => ['rapida'],
        ];

        foreach ($map as $slug => $categorySlugs) {
            $restaurant = Restaurant::query()->where('slug', $slug)->first();

            if (! $restaurant) {
                continue;
            }

            $categoryIds = RestaurantCategory::query()
                ->whereIn('slug', $categorySlugs)
                ->pluck('id');

            if ($categoryIds->isNotEmpty()) {
                $restaurant->categories()->sync($categoryIds);
            }
        }
    }
}
